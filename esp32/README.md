# ESP32 Sensor Node — Nyarugenge Mini Market

## Wiring Diagram

```
ESP32              Component
──────────────────────────────────────
GPIO 4      ←──── DHT11  DATA  (+ 10kΩ pull-up to 3.3V)
3.3V        ←──── DHT11  VCC
GND         ←──── DHT11  GND

GPIO 32     ←──── MQ-135 AO  (Analog Out)
5V          ←──── MQ-135 VCC  (needs 5V for heater)
GND         ←──── MQ-135 GND

GPIO 16  ──►──── Green  LED (+) → 220Ω → GND
GPIO 17  ──►──── Yellow LED (+) → 220Ω → GND
GPIO 18  ──►──── Red    LED (+) → 220Ω → GND
```

## LED Behaviour

| LED    | Meaning                              |
|--------|--------------------------------------|
| Green  | All sensors within safe range        |
| Yellow | One value within 10–15% of limit     |
| Red    | At least one threshold exceeded      |

On boot: all three LEDs light for 0.8s, then MQ-135 warm-up (yellow for 3s in dev, 20s in production).

## Thresholds (local LED + server-side alerts)

| Parameter   | Min    | Max     |
|-------------|--------|---------|
| Temperature | 5 °C   | 35 °C   |
| Humidity    | 20 %   | 80 %    |
| Gas Level   | —      | 400 ppm |

## Required Arduino Libraries

Install via **Arduino IDE → Tools → Manage Libraries**:

| Library                  | Author    | Version |
|--------------------------|-----------|---------|
| DHT sensor library       | Adafruit  | 1.4.x   |
| Adafruit Unified Sensor  | Adafruit  | 1.1.x   |
| ArduinoJson              | bblanchon | 7.x     |

## Board Setup

1. In Arduino IDE go to **File → Preferences**
2. Add to "Additional Boards Manager URLs":
   ```
   https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json
   ```
3. **Tools → Board → Boards Manager** → search "esp32" → Install **esp32 by Espressif Systems**
4. Select: **Tools → Board → ESP32 Arduino → ESP32 Dev Module**
5. Upload speed: **115200**

## Configuration (edit before flashing)

Open `nyarugenge_sensor.ino` and update:

```cpp
#define WIFI_SSID     "YOUR_WIFI_SSID"
#define WIFI_PASSWORD "YOUR_WIFI_PASSWORD"
#define DEVICE_UID    "ESP32-A1B2C3"   // Must match admin dashboard
#define SERVER_URL    "http://192.168.1.100/NYARUGENGE-Mini-Market/public/api/iot/readings"
```

### Finding your server IP (XAMPP)
Open `cmd` and run `ipconfig` — look for **IPv4 Address** on your WiFi adapter.

## API Endpoint

```
POST /api/iot/readings
Content-Type: application/json

{
  "device_uid":   "ESP32-A1B2C3",
  "temperature":  28.5,
  "humidity":     65.2,
  "gas_level":    210.0
}
```

Response `201 Created`:
```json
{
  "success": true,
  "message": "Sensor reading received.",
  "data": { ... }
}
```

## Serial Monitor Output (115200 baud)

```
==============================================
  Nyarugenge Mini Market — Sensor Node
  Device UID : ESP32-A1B2C3
==============================================
[MQ-135] Warming up sensor...
[DHT11] Initialized on GPIO 4
[WiFi] Connecting to MyWiFi.........
[WiFi] Connected! IP: 192.168.1.105
[READY] Sending every 30s
──────────────────────────────
  Temperature : 27.40 °C
  Humidity    : 62.00 %
  Gas Level   : 198.43 ppm
  Raw ADC     : 813
──────────────────────────────
[LED] 🟢 NORMAL — all values safe
[HTTP] POST → http://192.168.1.100/.../api/iot/readings
[HTTP] Response 201: {"success":true,...}
```

## Production Notes

- Increase MQ-135 warm-up: `delay(20000)` (20 seconds minimum)
- Increase `SEND_INTERVAL_MS` to `60000` (1 minute) for battery-powered nodes
- For HTTPS servers, use `WiFiClientSecure` and add the server certificate
- Each ESP32 must have a **unique** `DEVICE_UID` registered in the admin dashboard under **IoT Devices**
