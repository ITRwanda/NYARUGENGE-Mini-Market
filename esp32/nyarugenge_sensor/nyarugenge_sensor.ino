/**
 * ============================================================
 *  NYARUGENGE MINI MARKET — IoT Sensor Node
 *  Target: ESP32 (any 38-pin or 30-pin variant)
 * ============================================================
 *
 *  WIRING
 *  ──────────────────────────────────────────────────────────
 *  Sensor / Component     ESP32 Pin   Notes
 *  ─────────────────────  ─────────   ──────────────────────
 *  DHT11  DATA            GPIO 4      10kΩ pull-up to 3.3V
 *  DHT11  VCC             3.3V
 *  DHT11  GND             GND
 *
 *  MQ-135 AO (analog)     GPIO 32     ADC1_CH4 (0-3.3V)
 *  MQ-135 VCC             5V          Needs 5V for heater
 *  MQ-135 GND             GND
 *
 *  Green  LED (Normal)    GPIO 16     220Ω resistor to GND
 *  Yellow LED (Warning)   GPIO 17     220Ω resistor to GND
 *  Red    LED (Danger)    GPIO 18     220Ω resistor to GND
 *
 *  ──────────────────────────────────────────────────────────
 *  REQUIRED LIBRARIES (install via Arduino Library Manager)
 *  ──────────────────────────────────────────────────────────
 *  - DHT sensor library  by Adafruit  (v1.4.x)
 *  - Adafruit Unified Sensor          (dependency)
 *  - ArduinoJson                      (v7.x)
 *  - WiFi                             (built-in ESP32)
 *  - HTTPClient                       (built-in ESP32)
 * ============================================================
 */

#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "DHT.h"

// ─── USER CONFIGURATION ──────────────────────────────────────
// Change these values for each device you deploy

#define WIFI_SSID        "Net"       // ← your WiFi name
#define WIFI_PASSWORD    "00000000"    // ← your WiFi password

// Must match a device registered in the admin dashboard
#define DEVICE_UID       "ESP32-14D412"          // ← unique per device

// Server URL — use your local IP when running XAMPP locally
// e.g. "http://192.168.1.100/NYARUGENGE-Mini-Market/public/api/iot/readings"
// For artisan serve: "http://192.168.1.102:8000/api/iot/readings"
// For production: "https://yourdomain.rw/api/iot/readings"
#define SERVER_URL       "http://192.168.1.102:8000/api/iot/readings"

// How often to send data (milliseconds)
#define SEND_INTERVAL_MS  30000   // 30 seconds

// ─── PIN DEFINITIONS ─────────────────────────────────────────
#define DHT_PIN          4        // DHT11 DATA
#define DHT_TYPE         DHT11

#define MQ135_PIN        34       // MQ-135 Analog Out (ADC1)

#define LED_GREEN        16       // Normal (all values safe)
#define LED_YELLOW       17       // Warning (one value near limit)
#define LED_RED          18       // Danger  (threshold breached)

// ─── THRESHOLDS (local LED logic — mirrors server thresholds) ─
#define TEMP_MAX         35.0f    // °C
#define TEMP_MIN          5.0f    // °C
#define HUMIDITY_MAX     80.0f    // %
#define HUMIDITY_MIN     20.0f    // %
#define GAS_MAX         130.0f    // ppm equivalent (raw ADC mapped)

// MQ-135 raw ADC → ppm mapping (linear approximation)
// Calibrate this for your specific sensor & environment
#define MQ135_ADC_MAX   4095      // ESP32 ADC 12-bit
#define MQ135_PPM_MAX   1000.0f   // ppm at ADC max

// ─── GLOBALS ─────────────────────────────────────────────────
DHT dht(DHT_PIN, DHT_TYPE);

unsigned long lastSendTime = 0;
bool wifiConnected         = false;
int  failCount             = 0;

// ─── FUNCTION DECLARATIONS ───────────────────────────────────
void connectWifi();
float readGasPPM();
void  setStatusLED(float temp, float humidity, float gas);
bool  postReading(float temp, float humidity, float gas);
void  blinkLED(int pin, int times, int delayMs);

// ─────────────────────────────────────────────────────────────
void setup() {
    Serial.begin(115200);
    delay(500);

    Serial.println();
    Serial.println("==============================================");
    Serial.println("  Nyarugenge Mini Market — Sensor Node");
    Serial.print  ("  Device UID : ");
    Serial.println(DEVICE_UID);
    Serial.println("==============================================");

    // LED pins
    pinMode(LED_GREEN,  OUTPUT);
    pinMode(LED_YELLOW, OUTPUT);
    pinMode(LED_RED,    OUTPUT);

    // Boot indication — all LEDs on briefly
    digitalWrite(LED_GREEN,  HIGH);
    digitalWrite(LED_YELLOW, HIGH);
    digitalWrite(LED_RED,    HIGH);
    delay(800);
    digitalWrite(LED_GREEN,  LOW);
    digitalWrite(LED_YELLOW, LOW);
    digitalWrite(LED_RED,    LOW);

    // MQ-135 warm-up (allow ~20s minimum in production)
    Serial.println("[MQ-135] Warming up sensor...");
    // Show yellow during warm-up
    digitalWrite(LED_YELLOW, HIGH);
    delay(3000);   // shortened for dev; increase to 20000 in production
    digitalWrite(LED_YELLOW, LOW);

    // DHT11
    dht.begin();
    Serial.println("[DHT11] Initialized on GPIO " + String(DHT_PIN));

    // WiFi
    connectWifi();

    Serial.println("[READY] Sending every " + String(SEND_INTERVAL_MS / 1000) + "s");
}

// ─────────────────────────────────────────────────────────────
void loop() {
    // Reconnect WiFi if dropped
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[WiFi] Connection lost — reconnecting...");
        wifiConnected = false;
        connectWifi();
    }

    unsigned long now = millis();
    if (now - lastSendTime >= SEND_INTERVAL_MS) {
        lastSendTime = now;

        // ── Read sensors ───────────────────────────────────
        float humidity    = dht.readHumidity();
        float temperature = dht.readTemperature();  // Celsius
        float gasPPM      = readGasPPM();

        // Check for DHT read failure
        if (isnan(humidity) || isnan(temperature)) {
            Serial.println("[DHT11] ERROR: Failed to read sensor!");
            blinkLED(LED_RED, 3, 200);
            return;
        }

        // ── Print to serial monitor ────────────────────────
        Serial.println("──────────────────────────────");
        Serial.print  ("  Temperature : "); Serial.print(temperature); Serial.println(" °C");
        Serial.print  ("  Humidity    : "); Serial.print(humidity);    Serial.println(" %");
        Serial.print  ("  Gas Level   : "); Serial.print(gasPPM);      Serial.println(" ppm");
        Serial.print  ("  Raw ADC     : "); Serial.println(analogRead(MQ135_PIN));
        Serial.println("──────────────────────────────");

        // ── Update status LEDs ─────────────────────────────
        setStatusLED(temperature, humidity, gasPPM);

        // ── POST to server ─────────────────────────────────
        if (wifiConnected) {
            bool ok = postReading(temperature, humidity, gasPPM);
            if (ok) {
                failCount = 0;
                // Brief green flash on success
                blinkLED(LED_GREEN, 1, 100);
            } else {
                failCount++;
                Serial.print("[HTTP] Fail count: ");
                Serial.println(failCount);
                // After 5 consecutive failures, blink red
                if (failCount >= 5) {
                    blinkLED(LED_RED, 2, 150);
                }
            }
        } else {
            Serial.println("[HTTP] Skipping — no WiFi");
            blinkLED(LED_YELLOW, 2, 150);
        }
    }
}

// ─────────────────────────────────────────────────────────────
// Connect to WiFi with retry
// ─────────────────────────────────────────────────────────────
void connectWifi() {
    Serial.print("[WiFi] Connecting to ");
    Serial.print(WIFI_SSID);

    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    int attempts = 0;
    while (WiFi.status() != WL_CONNECTED && attempts < 20) {
        delay(500);
        Serial.print(".");
        attempts++;
        // Alternate yellow blink during connection
        digitalWrite(LED_YELLOW, (attempts % 2 == 0) ? HIGH : LOW);
    }
    digitalWrite(LED_YELLOW, LOW);

    if (WiFi.status() == WL_CONNECTED) {
        wifiConnected = true;
        Serial.println();
        Serial.print("[WiFi] Connected! IP: ");
        Serial.println(WiFi.localIP());
        blinkLED(LED_GREEN, 3, 100);
    } else {
        wifiConnected = false;
        Serial.println();
        Serial.println("[WiFi] FAILED — will retry on next cycle");
        blinkLED(LED_RED, 5, 100);
    }
}

// ─────────────────────────────────────────────────────────────
// Read MQ-135 and convert raw ADC to approximate ppm
// ─────────────────────────────────────────────────────────────
float readGasPPM() {
    // Average 10 readings for stability
    long   sum     = 0;
    const  int N   = 10;
    for (int i = 0; i < N; i++) {
        sum += analogRead(MQ135_PIN);
        delay(5);
    }
    float avgADC = (float)sum / N;

    // Linear mapping: 0 ADC = 0 ppm, MQ135_ADC_MAX = MQ135_PPM_MAX
    float ppm = (avgADC / MQ135_ADC_MAX) * MQ135_PPM_MAX;

    return ppm;
}

// ─────────────────────────────────────────────────────────────
// Set status LEDs based on current readings
//   Green  = all values within safe range
//   Yellow = any value is within 10% of its limit (warning)
//   Red    = any value has exceeded its limit (danger)
// ─────────────────────────────────────────────────────────────
void setStatusLED(float temp, float humidity, float gas) {
    bool danger  = false;
    bool warning = false;

    // Temperature checks
    if (temp > TEMP_MAX || temp < TEMP_MIN) {
        danger = true;
    } else if (temp > TEMP_MAX * 0.90f || temp < TEMP_MIN * 1.10f) {
        warning = true;
    }

    // Humidity checks
    if (humidity > HUMIDITY_MAX || humidity < HUMIDITY_MIN) {
        danger = true;
    } else if (humidity > HUMIDITY_MAX * 0.90f || humidity < HUMIDITY_MIN * 1.10f) {
        warning = true;
    }

    // Gas checks
    if (gas > GAS_MAX) {
        danger = true;
    } else if (gas > GAS_MAX * 0.85f) {
        warning = true;
    }

    // Apply LED state
    if (danger) {
        digitalWrite(LED_GREEN,  LOW);
        digitalWrite(LED_YELLOW, LOW);
        digitalWrite(LED_RED,    HIGH);
        Serial.println("[LED] 🔴 DANGER — threshold exceeded");
    } else if (warning) {
        digitalWrite(LED_GREEN,  LOW);
        digitalWrite(LED_YELLOW, HIGH);
        digitalWrite(LED_RED,    LOW);
        Serial.println("[LED] 🟡 WARNING — approaching limit");
    } else {
        digitalWrite(LED_GREEN,  HIGH);
        digitalWrite(LED_YELLOW, LOW);
        digitalWrite(LED_RED,    LOW);
        Serial.println("[LED] 🟢 NORMAL — all values safe");
    }
}

// ─────────────────────────────────────────────────────────────
// POST sensor reading to the web server API
// Endpoint: POST /api/iot/readings
// ─────────────────────────────────────────────────────────────
bool postReading(float temp, float humidity, float gas) {
    HTTPClient http;
    http.begin(SERVER_URL);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept",       "application/json");
    http.setTimeout(10000); // 10s timeout

    // Build JSON payload
    StaticJsonDocument<256> doc;
    doc["device_uid"]  = DEVICE_UID;
    doc["temperature"] = round(temp * 100.0f) / 100.0f;   // 2 decimal places
    doc["humidity"]    = round(humidity * 100.0f) / 100.0f;
    doc["gas_level"]   = round(gas * 100.0f) / 100.0f;

    String payload;
    serializeJson(doc, payload);

    Serial.print("[HTTP] POST → ");
    Serial.println(SERVER_URL);
    Serial.print("[HTTP] Body: ");
    Serial.println(payload);

    int httpCode = http.POST(payload);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.print("[HTTP] Response ");
        Serial.print(httpCode);
        Serial.print(": ");
        Serial.println(response);
        http.end();
        return (httpCode == 201 || httpCode == 200);
    } else {
        Serial.print("[HTTP] ERROR: ");
        Serial.println(http.errorToString(httpCode));
        http.end();
        return false;
    }
}

// ─────────────────────────────────────────────────────────────
// Blink a single LED N times
// ─────────────────────────────────────────────────────────────
void blinkLED(int pin, int times, int delayMs) {
    bool prevState = digitalRead(pin);
    for (int i = 0; i < times; i++) {
        digitalWrite(pin, HIGH);
        delay(delayMs);
        digitalWrite(pin, LOW);
        delay(delayMs);
    }
    digitalWrite(pin, prevState);  // restore previous state
}
