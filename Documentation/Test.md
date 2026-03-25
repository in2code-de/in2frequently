# Test Backend

Bitte teste die Sichtbarkeit eines Content Elements durch Änderung der Werte
`tt_content.tx_in2frequently_starttime` und `tt_content.tx_in2frequently_endtime`

Ob das Element (z.B. mit uid=1) sichtbar ist, kannst du via CURL testen:
```
curl -s https://in2frequently.ddev.site/example | grep 'id="c1"'
```

Testcases (für beispielsweise aktuellem Datum "Mittwoch, 25.3.2026 13:00"):

| `tx_in2frequently_starttime` | `tx_in2frequently_endtime` | Sichtbarkeit |
|------------------------------|----------------------------|--------------|
| Every 20th                   | Every 26th                 | ja           |
| Every 25th                   | Every 26th                 | ja           |
| Every 24th                   | Every 25th                 | nein         |
| 55 12 * * *                  | 0 14 * * *                 | ja           |
| every monday at 8:00         | every thursday at 18:00    | ja           |
| every day at 3 AM            | every day at 2 PM          | ja           |
| every day at 3 AM            | every day at 11 AM         | nein         |