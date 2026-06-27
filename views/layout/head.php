<head>
    <script>
        // 1. Apagamos la pantalla al instante (antes de que el body exista)
        document.documentElement.style.visibility = 'hidden';

        // 2. Prendemos la pantalla SOLO cuando el CSS y todo el HTML terminó de cargar
        window.addEventListener('load', function() {
            document.documentElement.style.visibility = '';
            document.body.classList.add('animate-fadeIn'); // Le da un toque suave al aparecer
        });
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'MediTurnos — Plataforma Médica' ?></title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22 fill=%22%232F4550%22>✚</text></svg>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;500;700&family=DM+Serif+Display&display=swap" rel="stylesheet">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <link rel="preload" href="/mediturnos/views/layout/tailwind.css" as="style">

    <link rel="stylesheet" href="/mediturnos/views/layout/tailwind.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        charcoal: '#2F4550',
                        slate: '#586F7C',
                        lightblue: '#B8DBD9',
                        ghost: '#F4F4F9',
                    },
                    fontFamily: {
                        sans: ['"DM Sans"', 'sans-serif'],
                        serif: ['"DM Serif Display"', 'serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .flatpickr-months .flatpickr-month {
            color: #2F4550 !important;
        }

        span.flatpickr-weekday {
            color: #586F7C !important;
            font-weight: 700;
        }

        .flatpickr-day.selected {
            background: #2F4550 !important;
            border-color: #2F4550 !important;
            color: #fff !important;
        }

        .flatpickr-day.today {
            border-color: #B8DBD9 !important;
        }

        .flatpickr-day.flatpickr-disabled {
            color: #ccc !important;
            background: transparent !important;
        }


        body svg {
            visibility: hidden;
        }

        svg[class*="w-"],
        svg[class*="h-"] {
            visibility: visible !important;
        }

        .ts-wrapper.single .ts-control {
            background-color: #F8FAFC;
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #2F4550;
            box-shadow: none;
            transition: all 0.2s;
            cursor: text;
        }

        .ts-wrapper.single.focus .ts-control {
            background-color: #ffffff;
            border-color: #586F7C;
            box-shadow: 0 0 0 3px rgba(88, 111, 124, 0.1);
        }

        .ts-dropdown {
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #E5E7EB;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 0.25rem;
            z-index: 9999;
        }

        .ts-dropdown .option {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #2F4550;
        }

        .ts-dropdown .option.active,
        .ts-dropdown .option:hover {
            background-color: #F8FAFC;
            color: #2F4550;
            font-weight: 600;
        }
    </style>
</head>