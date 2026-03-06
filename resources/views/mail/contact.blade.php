<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo messaggio - NBA Universe</title>
</head>
<body style="
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 40px 20px;
">
    <div style="
        max-width: 600px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    ">
        {{-- Header email con colori ufficiali NBA --}}
        <div style="
            background: #1D428A;
            padding: 2rem;
            text-align: center;
        ">
            <div style="
                display: inline-block;
                background: #C8102E;
                color: #fff;
                font-size: 1.5rem;
                font-weight: 900;
                padding: 0.3rem 1rem;
                border-radius: 4px;
                letter-spacing: 0.1em;
                margin-bottom: 0.5rem;
            ">NBA</div>
            <div style="color: #ffffff; font-size: 1rem; letter-spacing: 0.05em;">
                Nuovo messaggio dal sito
            </div>
        </div>

        {{-- Corpo del messaggio --}}
        <div style="padding: 2rem;">

            <h2 style="
                color: #1D428A;
                font-size: 1.25rem;
                margin-bottom: 1.5rem;
                border-bottom: 2px solid #1D428A;
                padding-bottom: 0.5rem;
            ">
                Hai ricevuto un nuovo messaggio di contatto
            </h2>

            {{-- Campo: Da (email mittente) --}}
            <div style="margin-bottom: 1.25rem;">
                <div style="
                    font-size: 0.75rem;
                    font-weight: 700;
                    letter-spacing: 0.1em;
                    text-transform: uppercase;
                    color: #C8102E;
                    margin-bottom: 0.3rem;
                ">📧 Da</div>
                <div style="
                    background: #f8f9ff;
                    border: 1px solid #e0e4f0;
                    border-left: 4px solid #1D428A;
                    padding: 0.75rem 1rem;
                    border-radius: 4px;
                    color: #333;
                ">{{ $dati['email'] }}</div>
            </div>

            {{-- Campo: Oggetto --}}
            <div style="margin-bottom: 1.25rem;">
                <div style="
                    font-size: 0.75rem;
                    font-weight: 700;
                    letter-spacing: 0.1em;
                    text-transform: uppercase;
                    color: #C8102E;
                    margin-bottom: 0.3rem;
                ">📌 Oggetto</div>
                <div style="
                    background: #f8f9ff;
                    border: 1px solid #e0e4f0;
                    border-left: 4px solid #1D428A;
                    padding: 0.75rem 1rem;
                    border-radius: 4px;
                    color: #333;
                    font-weight: 600;
                ">{{ $dati['oggetto'] }}</div>
            </div>

            {{-- Campo: Messaggio --}}
            <div style="margin-bottom: 1.5rem;">
                <div style="
                    font-size: 0.75rem;
                    font-weight: 700;
                    letter-spacing: 0.1em;
                    text-transform: uppercase;
                    color: #C8102E;
                    margin-bottom: 0.3rem;
                ">💬 Messaggio</div>
                <div style="
                    background: #f8f9ff;
                    border: 1px solid #e0e4f0;
                    border-left: 4px solid #1D428A;
                    padding: 1rem;
                    border-radius: 4px;
                    color: #333;
                    line-height: 1.7;
                    white-space: pre-wrap;
                ">{{ $dati['messaggio'] }}</div>
            </div>

        </div>

        {{-- Footer email --}}
        <div style="
            background: #f4f4f4;
            padding: 1rem 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #888;
            border-top: 1px solid #e0e0e0;
        ">
            Inviato automaticamente da <strong>NBA Universe</strong> — {{ date('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>