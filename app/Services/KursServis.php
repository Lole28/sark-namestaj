<?php

/**
 * Potrošnja spoljnog web servisa: dohvatanje kursa EUR -> RSD.
 * Rezultat se kešira 6h u /storage. Ako servis nije dostupan (npr. nema
 * interneta na lokalnom serveru), koristi se rezervna vrednost iz config-a.
 */
class KursServis
{
    private const TTL          = 21600; // uspešan odgovor važi 6 sati
    private const TTL_NEUSPEH  = 900;   // ako servis padne, ne diramo ga 15 min

    public static function eurRsd(): array
    {
        $kes = BASE_PATH . '/storage/kurs.json';

        if (is_file($kes)) {
            $sacuvano = json_decode((string) file_get_contents($kes), true);
            if (is_array($sacuvano) && isset($sacuvano['kurs'])) {
                $vazi = !empty($sacuvano['_ok']) ? self::TTL : self::TTL_NEUSPEH;
                if ((time() - filemtime($kes)) < $vazi) {
                    unset($sacuvano['_ok']);
                    return $sacuvano;
                }
            }
        }

        $rezultat = self::dohvati();

        if ($rezultat !== null) {
            @file_put_contents($kes, json_encode($rezultat + ['_ok' => true]));
            return $rezultat;
        }

        $rezerva = [
            'kurs'  => (float) $GLOBALS['config']['app']['kurs_rezerva'],
            'datum' => date('Y-m-d'),
            'izvor' => 'rezervna vrednost (servis nedostupan)',
        ];
        @file_put_contents($kes, json_encode($rezerva + ['_ok' => false]));
        return $rezerva;
    }

    private static function dohvati(): ?array
    {
        $url = $GLOBALS['config']['app']['kurs_api'];
        $kontekst = stream_context_create([
            'http' => ['timeout' => 3, 'ignore_errors' => true],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);

        $odgovor = @file_get_contents($url, false, $kontekst);
        if ($odgovor === false) {
            return null;
        }

        $json = json_decode($odgovor, true);
        $kurs = $json['rates']['RSD'] ?? null;
        if (!$kurs) {
            return null;
        }

        return [
            'kurs'  => round((float) $kurs, 2),
            'datum' => $json['time_last_update_utc'] ?? date('Y-m-d'),
            'izvor' => 'open.er-api.com',
        ];
    }
}
