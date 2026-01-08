<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        .header { text-align: center; margin-bottom: 20px; }
        .grid { width: 100%; margin-bottom: 20px; }
        .grid td { padding: 4px 6px; vertical-align: top; }
        .totals { margin-top: 20px; }
        .totals td { padding: 6px; font-size: 14px; }
        .meta { font-size: 11px; color: #4b5563; margin-top: 40px; }
        h1 { font-size: 22px; margin-bottom: 5px; }
        h2 { font-size: 14px; margin-bottom: 2px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 11px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facture {{ $facture->numero }}</h1>
        <div>{{ config('app.name', 'FREESURF') }}</div>
        <span class="badge" style="background:#fee2e2;color:#b91c1c;">{{ strtoupper($facture->statut) }}</span>
    </div>

    <table class="grid">
        <tr>
            <td width="50%">
                <h2>Client</h2>
                <div><strong>{{ $facture->client->nom }}</strong></div>
                <div>Code: {{ $facture->client->code }}</div>
                <div>T&eacute;l: {{ $facture->client->telephone }}</div>
            </td>
            <td width="50%">
                <h2>Dossier</h2>
                <div>P&eacute;riode: {{ $facture->periode }}</div>
                <div>Emission: {{ optional($facture->issued_at)->format('d/m/Y H:i') ?? 'NC' }}</div>
                <div>Ech&eacute;ance: {{ optional($facture->due_date)->format('d/m/Y') ?? 'NC' }}</div>
            </td>
        </tr>
    </table>

    <table class="grid" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th style="padding:6px;text-align:left;">Description</th>
                <th style="padding:6px;text-align:right;">Montant (XAF)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding:6px;">Acc&egrave;s Internet haut d&eacute;bit - P&eacute;riode {{ $facture->periode }}</td>
                <td style="padding:6px;text-align:right;">{{ number_format($facture->montant, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td width="70%" style="text-align:right;"><strong>Total &agrave; payer</strong></td>
            <td width="30%" style="text-align:right;"><strong>{{ number_format($facture->montant, 2, ',', ' ') }} XAF</strong></td>
        </tr>
    </table>

    <div class="meta">
        <p>Cette facture a &eacute;t&eacute; g&eacute;n&eacute;r&eacute;e automatiquement via le portail CuWiP. Pour toute question, contactez support@freesurf.com.</p>
        <p>Statut actuel: {{ strtoupper($facture->statut) }} - Paiement re&ccedil;u le {{ optional($facture->paid_at)->format('d/m/Y H:i') ?? 'En attente' }}.</p>
    </div>
</body>
</html>

