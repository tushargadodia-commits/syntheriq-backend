<?php
session_start();
$error = '';

if (isset($_POST['login'])) {
    $username = trim(strtolower($_POST['username']));
    $password = trim($_POST['password']);

    if ($username === 'tushar' && $password === '00001111') {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = 'Tushar';
        header("Location: index.php");
        exit();
    } elseif ($username === 'nishant' && $password === 'nishant@123') {
        $_SESSION['role'] = 'subadmin';
        $_SESSION['username'] = 'Nishant';
        header("Location: index.php");
        exit();
    } elseif ($username === 'ajay' && $password === 'ajay@123') {
        $_SESSION['role'] = 'subadmin';
        $_SESSION['username'] = 'Ajay';
        header("Location: index.php");
        exit();
    } elseif (($username === 'varunmaruya@syntheriq.com' || $username === 'varun') && $password === 'varun8287') {
        $_SESSION['role'] = 'subadmin';
        $_SESSION['username'] = 'Varun';
        header("Location: index.php");
        exit();
    } elseif (($username === 'akankshamaurya@syntheriq.com' || $username === 'akanksha') && $password === 'akku525650') {
        $_SESSION['role'] = 'subadmin';
        $_SESSION['username'] = 'Akanksha';
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Syncro Leads Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body id="mainBody" class="bg-slate-50 text-slate-900 font-sans min-h-screen flex flex-col md:flex-row transition-colors duration-300">

    <!-- Top Right Dark Mode Toggle Button -->
    <button onclick="toggleDarkMode()" id="modeBtn" class="absolute top-6 right-6 z-30 bg-white hover:bg-slate-100 text-slate-800 px-4 py-2 rounded-full text-xs font-bold shadow-md border border-slate-200 flex items-center gap-2 transition">
        🌙 Dark Mode
    </button>

    <!-- Left Brand / Banner Section -->
    <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-purple-950 via-purple-900 to-indigo-950 text-white p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Clean, Boxless, Large Logo Section -->
        <div class="relative z-10 flex items-center space-x-4">
            <img src="logo.png" alt="Logo" class="h-16 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span class="text-white text-3xl font-black hidden">S</span>
            <div>
                <h1 class="text-xl font-extrabold tracking-wide">Syncro <span class="text-purple-400">Manager</span></h1>
                <span class="text-[11px] text-purple-300 block tracking-wider uppercase font-semibold">Syntheriq Technologies</span>
            </div>
        </div>

        <div class="relative z-10 my-auto max-w-lg space-y-4">
            <div class="inline-block bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full text-[11px] font-semibold border border-white/20 text-purple-200">
                ⚡ ENTERPRISE CRM PORTAL
            </div>
            <h2 class="text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                Seamless Lead & Pipeline Control.
            </h2>
            <p class="text-purple-200 text-sm leading-relaxed font-medium">
                Manage client inquiries, track follow-ups, and handle partner operations securely with high precision and real-time analytics.
            </p>
        </div>

        <div class="relative z-10 text-xs text-purple-300/80 font-medium">
            © 2026 SYNTHERIQ TECHNOLOGIES. ALL RIGHTS RESERVED.
        </div>
    </div>

    <!-- Right Login Form Section -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-12 relative min-h-screen md:min-h-0" id="rightContainer">
        <div class="bg-white p-8 md:p-10 rounded-3xl shadow-2xl max-w-md w-full border border-slate-100 transition-all duration-300" id="loginCard">
            
            <div class="mb-8">
                <h3 class="text-2xl font-extrabold tracking-tight text-slate-900" id="welcomeText">Welcome Back! 👋</h3>
                <p class="text-slate-500 text-xs font-semibold mt-1" id="subText">Please sign in to your CRM account.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="bg-red-500/10 border border-red-500/30 text-red-600 p-3.5 rounded-2xl mb-6 text-xs text-center font-bold">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Username / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm">👤</span>
                        <input type="text" name="username" required class="w-full pl-11 pr-4 py-3.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-600 focus:bg-white transition font-medium placeholder-slate-400" placeholder="Enter username or email">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm">🔒</span>
                        <input type="password" id="passwordField" name="password" required class="w-full pl-11 pr-12 py-3.5 text-sm bg-slate-50 border border-slate-200 text-slate-900 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-600 focus:bg-white transition font-medium placeholder-slate-400" placeholder="••••••••">
                        
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition text-base focus:outline-none">
                            <span id="eyeIcon">👁️</span>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="w-full bg-gradient-to-r from-purple-700 to-indigo-600 hover:from-purple-600 hover:to-indigo-500 text-white py-4 rounded-2xl text-sm font-extrabold transition shadow-lg shadow-purple-600/30 tracking-wide mt-2">
                    SIGN IN TO CRM
                </button>
            </form>

            <div class="mt-8 text-center border-t border-slate-100 pt-5">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider" id="footerText">SYNCRO (MANAGER) • SECURE PORTAL</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwdInput = document.getElementById('passwordField');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.innerText = '🙈';
            } else {
                pwdInput.type = 'password';
                eyeIcon.innerText = '👁️';
            }
        }

        function toggleDarkMode() {
            const body = document.getElementById('mainBody');
            const rightContainer = document.getElementById('rightContainer');
            const loginCard = document.getElementById('loginCard');
            const modeBtn = document.getElementById('modeBtn');
            const welcomeText = document.getElementById('welcomeText');
            const subText = document.getElementById('subText');
            const footerText = document.getElementById('footerText');
            const pwdInput = document.getElementById('passwordField');
            const userInput = document.querySelector('input[name="username"]');

            if (body.classList.contains('bg-slate-50')) {
                // Switch to Dark Mode
                body.classList.remove('bg-slate-50', 'text-slate-900');
                body.classList.add('bg-slate-950', 'text-slate-100');

                rightContainer.classList.remove('bg-slate-50');
                rightContainer.classList.add('bg-slate-950');

                loginCard.classList.remove('bg-white', 'border-slate-100');
                loginCard.classList.add('bg-slate-900', 'border-slate-800', 'shadow-2xl');

                welcomeText.classList.remove('text-slate-900');
                welcomeText.classList.add('text-white');

                subText.classList.remove('text-slate-500');
                subText.classList.add('text-slate-400');

                footerText.classList.remove('text-slate-400');
                footerText.classList.add('text-slate-500');

                pwdInput.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-900');
                pwdInput.classList.add('bg-slate-950', 'border-slate-800', 'text-white');

                userInput.classList.remove('bg-slate-50', 'border-slate-200', 'text-slate-900');
                userInput.classList.add('bg-slate-950', 'border-slate-800', 'text-white');

                modeBtn.innerHTML = '☀️ Light Mode';
                modeBtn.className = 'absolute top-6 right-6 z-30 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-full text-xs font-bold shadow-md border border-slate-800 flex items-center gap-2 transition';
            } else {
                // Switch to Light Mode
                body.classList.remove('bg-slate-950', 'text-slate-100');
                body.classList.add('bg-slate-50', 'text-slate-900');

                rightContainer.classList.remove('bg-slate-950');
                rightContainer.classList.add('bg-slate-50');

                loginCard.classList.remove('bg-slate-900', 'border-slate-800');
                loginCard.classList.add('bg-white', 'border-slate-100');

                welcomeText.classList.remove('text-white');
                welcomeText.classList.add('text-slate-900');

                subText.classList.remove('text-slate-400');
                subText.classList.add('text-slate-500');

                footerText.classList.remove('text-slate-500');
                footerText.classList.add('text-slate-400');

                pwdInput.classList.remove('bg-slate-950', 'border-slate-800', 'text-white');
                pwdInput.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-900');

                userInput.classList.remove('bg-slate-950', 'border-slate-800', 'text-white');
                userInput.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-900');

                modeBtn.innerHTML = '🌙 Dark Mode';
                modeBtn.className = 'absolute top-6 right-6 z-30 bg-white hover:bg-slate-100 text-slate-800 px-4 py-2 rounded-full text-xs font-bold shadow-md border border-slate-200 flex items-center gap-2 transition';
            }
        }
    </script>
</body>
</html>