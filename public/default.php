<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode | CIVIC Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,700;1,800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
        }
        .luxury-gradient {
            background: radial-gradient(circle at top right, #fff5f5 0%, #ffffff 100%);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="luxury-gradient h-screen flex items-center justify-center overflow-hidden">

    <div class="relative w-full max-w-2xl px-6 text-center">
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-red-50 rounded-full blur-3xl opacity-60"></div>
        <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-60"></div>

        <div class="relative">
            <div class="mb-12 flex justify-center">
                <div class="p-4 bg-white shadow-2xl shadow-red-900/5 rounded-3xl border border-gray-50">
                    <img src="images/logo.png" alt="CIVIC Logo" class="h-16 w-auto object-contain">
                </div>
            </div>

            <div class="flex justify-center mb-8">
                <div class="animate-float">
                    <div class="p-6 bg-[#800000] rounded-full shadow-2xl shadow-red-900/40">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.423 3.007a3 3 0 013.154 0l6.598 3.81a3 3 0 011.5 2.598v7.62a3 3 0 01-1.5 2.598l-6.598 3.81a3 3 0 01-3.154 0l-6.598-3.81a3 3 0 01-1.5-2.598v-7.62a3 3 0 011.5-2.598l6.598-3.81z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" stroke-width="2.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <h1 class="text-[12px] font-black uppercase tracking-[0.5em] text-[#800000] mb-4 italic">
                System Upgrade In Progress
            </h1>

            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6 tracking-tight">
                Kami Akan Segera <br> <span class="text-slate-400 italic">Kembali.</span>
            </h2>

            <p class="text-slate-500 text-sm md:text-base max-w-md mx-auto leading-relaxed mb-10">
                Saat ini kami sedang melakukan pemeliharaan rutin untuk meningkatkan pengalaman layanan <strong>CIVIC Platform</strong>. Mohon tunggu beberapa saat lagi.
            </p>

            <div class="max-w-xs mx-auto mb-10">
                <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-[#800000] w-2/3 rounded-full animate-[progress_2s_ease-in-out_infinite]"></div>
                </div>
                <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest mt-3">Optimizing Database & Security</p>
            </div>

            <div class="pt-8 border-t border-gray-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Butuh bantuan mendesak?
                </p>
                <a href="mailto:support@civic.id" class="text-[11px] font-black text-[#800000] uppercase tracking-widest hover:underline mt-2 inline-block">
                    Contact Administrator
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes progress {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</body>
</html>
