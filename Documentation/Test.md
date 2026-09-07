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

## Schaltminute

Der kritischste Fall ist die Minute, in der `tx_in2frequently_starttime` selbst feuert. Zum Testen
den Startausdruck auf die *laufende* Minute setzen (z.B. um 09:42 Uhr auf `42 9 * * *`) und den
Endausdruck einige Minuten später (`52 9 * * *`), danach Cache leeren und die Seite aufrufen.

| Zeitpunkt des Requests | Erwartung                                                  |
|------------------------|------------------------------------------------------------|
| 09:41:xx               | Element unsichtbar, Cache läuft spätestens 09:42:00 ab     |
| 09:42:00 - 09:42:59    | Element **sichtbar**, Cache läuft spätestens 09:52:00 ab   |
| 09:43:00               | Element sichtbar                                           |
| 09:52:30               | Element unsichtbar                                         |

Die Cache-Lebensdauer lässt sich am Response-Header `X-TYPO3-Cache-Lifetime` ablesen:

```
curl -sI https://in2frequently.ddev.site/example | grep -i cache-lifetime
```

Der Wert darf **nie** `0` sein - eine 0 bedeutet für TYPO3 "unbegrenzt" und die Seite würde bis
2037 im Cache liegen bleiben.
