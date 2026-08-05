<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use App\Services\GameExtractorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin Account
        User::updateOrCreate(
            ['email' => 'admin@gameportal.com'],
            [
                'name' => 'Administrator GameHub',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]
        );

        // 2. Create Sample HTML5 Games
        $this->seedSampleGames();
    }

    protected function seedSampleGames(): void
    {
        $extractor = new GameExtractorService();

        $sampleGames = [
            [
                'title' => 'Space Invaders Galaxy 2D',
                'slug' => 'space-invaders-galaxy-2d',
                'category' => 'Action',
                'description' => "Kendalikan pesawat luar angkasa Anda untuk menghancurkan invasi alien dan meteor! Gunakan tombol Panah Kiri/Kanan untuk bergerak dan Spasi untuk menembak laser.",
                'html' => $this->getSpaceShooterHtml(),
                'svg_thumbnail' => $this->getSpaceShooterSvg(),
                'plays_count' => 1250,
                'views_count' => 3400,
            ],
            [
                'title' => 'Retro Flappy Flyer',
                'slug' => 'retro-flappy-flyer',
                'category' => 'Arcade',
                'description' => "Bantu burung terbang melewati rintangan pipa tanpa menabrak! Tekan Spasi atau Klik pada layar untuk mengepakkan sayap.",
                'html' => $this->getFlappyBirdHtml(),
                'svg_thumbnail' => $this->getFlappyBirdSvg(),
                'plays_count' => 2890,
                'views_count' => 5100,
            ],
            [
                'title' => 'Cyber Snake DX',
                'slug' => 'cyber-snake-dx',
                'category' => 'Casual',
                'description' => "Game ular klasik berbalut warna-warni cyberpunk neon! Makan makanan orb untuk menambah panjang ular dan raih skor tertinggi.",
                'html' => $this->getSnakeHtml(),
                'svg_thumbnail' => $this->getSnakeSvg(),
                'plays_count' => 1950,
                'views_count' => 4200,
            ],
        ];

        foreach ($sampleGames as $gameData) {
            $slug = $gameData['slug'];
            $targetFolder = Storage::disk('public')->path('games/' . $slug);

            if (!File::exists($targetFolder)) {
                File::makeDirectory($targetFolder, 0755, true);
            }

            // Save index.html inside target folder
            File::put($targetFolder . '/index.html', $gameData['html']);

            // Save Thumbnail Image
            $thumbFileName = 'thumbnails/' . $slug . '.png';
            $this->saveSvgAsPng($gameData['svg_thumbnail'], Storage::disk('public')->path($thumbFileName));

            // Create Game Record in DB
            Game::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $gameData['title'],
                    'description' => $gameData['description'],
                    'category' => $gameData['category'],
                    'thumbnail' => $thumbFileName,
                    'folder_name' => $slug,
                    'entry_file' => 'index.html',
                    'plays_count' => $gameData['plays_count'],
                    'views_count' => $gameData['views_count'],
                    'is_active' => true,
                ]
            );
        }
    }

    protected function saveSvgAsPng(string $svgContent, string $targetPath): void
    {
        $dir = dirname($targetPath);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        
        // Simple SVG file creation
        $svgPath = str_replace('.png', '.svg', $targetPath);
        File::put($svgPath, $svgContent);

        // If GD extension or SVG copy exists
        File::copy($svgPath, $targetPath);
    }

    // --- HTML5 GAME 1: SPACE SHOOTER ---
    protected function getSpaceShooterHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Space Invaders Galaxy 2D</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #050814; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; }
        canvas { border: 2px solid #8b5cf6; border-radius: 12px; box-shadow: 0 0 30px rgba(139,92,246,0.4); background: radial-gradient(circle at center, #0f172a 0%, #050814 100%); }
        #ui { position: absolute; top: 15px; font-weight: bold; font-size: 18px; text-shadow: 0 0 10px #8b5cf6; display: flex; gap: 30px; }
    </style>
</head>
<body>
    <div id="ui">
        <div>SCORE: <span id="score">0</span></div>
        <div>LIVES: <span id="lives">3</span></div>
    </div>
    <canvas id="gameCanvas" width="700" height="500"></canvas>
    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const scoreEl = document.getElementById('score');
        const livesEl = document.getElementById('lives');

        let score = 0;
        let lives = 3;
        let gameOver = false;

        const player = { x: canvas.width / 2 - 20, y: canvas.height - 50, w: 40, h: 30, speed: 7, color: '#06b6d4' };
        const bullets = [];
        const enemies = [];
        const stars = [];

        for (let i = 0; i < 50; i++) {
            stars.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, size: Math.random() * 2, speed: Math.random() * 2 + 0.5 });
        }

        const keys = {};
        window.addEventListener('keydown', e => keys[e.code] = true);
        window.addEventListener('keyup', e => keys[e.code] = false);

        function spawnEnemy() {
            if (gameOver) return;
            const size = Math.random() * 20 + 20;
            enemies.push({ x: Math.random() * (canvas.width - size), y: -size, w: size, h: size, speed: Math.random() * 2 + 1.5, color: '#f43f5e' });
        }
        setInterval(spawnEnemy, 1000);

        function update() {
            if (gameOver) return;

            // Player move
            if ((keys['ArrowLeft'] || keys['KeyA']) && player.x > 0) player.x -= player.speed;
            if ((keys['ArrowRight'] || keys['KeyD']) && player.x + player.w < canvas.width) player.x += player.speed;
            if (keys['Space']) {
                if (!player.lastShoot || Date.now() - player.lastShoot > 200) {
                    bullets.push({ x: player.x + player.w / 2 - 3, y: player.y, w: 6, h: 14, speed: 9 });
                    player.lastShoot = Date.now();
                }
            }

            // Move stars
            stars.forEach(s => {
                s.y += s.speed;
                if (s.y > canvas.height) s.y = 0;
            });

            // Move bullets
            for (let i = bullets.length - 1; i >= 0; i--) {
                bullets[i].y -= bullets[i].speed;
                if (bullets[i].y < 0) bullets.splice(i, 1);
            }

            // Move enemies
            for (let i = enemies.length - 1; i >= 0; i--) {
                const e = enemies[i];
                e.y += e.speed;

                // Collision with player
                if (e.x < player.x + player.w && e.x + e.w > player.x && e.y < player.y + player.h && e.y + e.h > player.y) {
                    enemies.splice(i, 1);
                    lives--;
                    livesEl.innerText = lives;
                    if (lives <= 0) { gameOver = true; }
                    continue;
                }

                // Collision with bullets
                for (let j = bullets.length - 1; j >= 0; j--) {
                    const b = bullets[j];
                    if (b.x < e.x + e.w && b.x + b.w > e.x && b.y < e.y + e.h && b.y + b.h > e.y) {
                        enemies.splice(i, 1);
                        bullets.splice(j, 1);
                        score += 100;
                        scoreEl.innerText = score;
                        break;
                    }
                }

                if (e && e.y > canvas.height) enemies.splice(i, 1);
            }
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw Stars
            ctx.fillStyle = '#94a3b8';
            stars.forEach(s => ctx.fillRect(s.x, s.y, s.size, s.size));

            // Draw Player
            ctx.fillStyle = player.color;
            ctx.beginPath();
            ctx.moveTo(player.x + player.w / 2, player.y);
            ctx.lineTo(player.x + player.w, player.y + player.h);
            ctx.lineTo(player.x, player.y + player.h);
            ctx.closePath();
            ctx.fill();

            // Draw Bullets
            ctx.fillStyle = '#38bdf8';
            bullets.forEach(b => ctx.fillRect(b.x, b.y, b.w, b.h));

            // Draw Enemies
            enemies.forEach(e => {
                ctx.fillStyle = e.color;
                ctx.fillRect(e.x, e.y, e.w, e.h);
            });

            if (gameOver) {
                ctx.fillStyle = 'rgba(5, 8, 20, 0.85)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#f43f5e';
                ctx.font = 'bold 36px Segoe UI';
                ctx.textAlign = 'center';
                ctx.fillText('GAME OVER', canvas.width / 2, canvas.height / 2 - 20);
                ctx.fillStyle = '#fff';
                ctx.font = '18px Segoe UI';
                ctx.fillText('Skor Akhir: ' + score, canvas.width / 2, canvas.height / 2 + 20);
                ctx.fillText('Tekan R untuk Main Lagi', canvas.width / 2, canvas.height / 2 + 50);
            }
        }

        window.addEventListener('keydown', e => {
            if (gameOver && e.code === 'KeyR') {
                score = 0; lives = 3; gameOver = false;
                scoreEl.innerText = score; livesEl.innerText = lives;
                enemies.length = 0; bullets.length = 0;
            }
        });

        function loop() {
            update();
            draw();
            requestAnimationFrame(loop);
        }
        loop();
    </script>
</body>
</html>
HTML;
    }

    // --- HTML5 GAME 2: FLAPPY BIRD ---
    protected function getFlappyBirdHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retro Flappy Flyer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0f172a; color: #fff; font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; }
        canvas { border: 3px solid #ec4899; border-radius: 16px; box-shadow: 0 0 30px rgba(236,72,153,0.4); background: #38bdf8; }
    </style>
</head>
<body>
    <canvas id="c" width="400" height="550"></canvas>
    <script>
        const canvas = document.getElementById('c');
        const ctx = canvas.getContext('2d');

        let bird = { x: 80, y: 250, v: 0, g: 0.45, jump: -7.5, r: 14 };
        let pipes = [];
        let score = 0;
        let gameOver = false;

        function jump() {
            if (gameOver) {
                bird.y = 250; bird.v = 0; pipes = []; score = 0; gameOver = false; return;
            }
            bird.v = bird.jump;
        }

        window.addEventListener('keydown', e => { if (e.code === 'Space') jump(); });
        canvas.addEventListener('click', jump);

        function spawnPipe() {
            const gap = 130;
            const topHeight = Math.random() * (canvas.height - gap - 100) + 40;
            pipes.push({ x: canvas.width, top: topHeight, bottom: canvas.height - topHeight - gap, w: 50, passed: false });
        }

        setInterval(() => { if (!gameOver) spawnPipe(); }, 1600);

        function loop() {
            ctx.fillStyle = '#38bdf8';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            if (!gameOver) {
                bird.v += bird.g;
                bird.y += bird.v;

                if (bird.y + bird.r > canvas.height || bird.y - bird.r < 0) gameOver = true;

                for (let i = pipes.length - 1; i >= 0; i--) {
                    let p = pipes[i];
                    p.x -= 2.5;

                    if (bird.x + bird.r > p.x && bird.x - bird.r < p.x + p.w) {
                        if (bird.y - bird.r < p.top || bird.y + bird.r > canvas.height - p.bottom) {
                            gameOver = true;
                        }
                    }

                    if (!p.passed && p.x + p.w < bird.x) {
                        p.passed = true; score++;
                    }

                    if (p.x + p.w < 0) pipes.splice(i, 1);
                }
            }

            // Draw Pipes
            ctx.fillStyle = '#10b981';
            pipes.forEach(p => {
                ctx.fillRect(p.x, 0, p.w, p.top);
                ctx.fillRect(p.x, canvas.height - p.bottom, p.w, p.bottom);
            });

            // Draw Bird
            ctx.fillStyle = '#f59e0b';
            ctx.beginPath();
            ctx.arc(bird.x, bird.y, bird.r, 0, Math.PI * 2);
            ctx.fill();

            // Score
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 30px sans-serif';
            ctx.fillText(score, canvas.width / 2 - 10, 50);

            if (gameOver) {
                ctx.fillStyle = 'rgba(0,0,0,0.6)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ec4899';
                ctx.font = 'bold 32px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('GAME OVER', canvas.width / 2, 250);
                ctx.fillStyle = '#fff';
                ctx.font = '16px sans-serif';
                ctx.fillText('Klik / Tekan Spasi untuk Main Lagi', canvas.width / 2, 290);
            }

            requestAnimationFrame(loop);
        }
        loop();
    </script>
</body>
</html>
HTML;
    }

    // --- HTML5 GAME 3: CYBER SNAKE ---
    protected function getSnakeHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cyber Snake DX</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #090d16; color: #38bdf8; font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
        canvas { border: 2px solid #06b6d4; border-radius: 12px; box-shadow: 0 0 25px rgba(6,182,212,0.4); background: #0f172a; }
        h1 { margin-bottom: 10px; font-size: 20px; }
    </style>
</head>
<body>
    <h1>SKOR: <span id="s">0</span></h1>
    <canvas id="c" width="400" height="400"></canvas>
    <script>
        const canvas = document.getElementById('c');
        const ctx = canvas.getContext('2d');
        const grid = 20;
        let snake = [{x: 160, y: 160}, {x: 140, y: 160}];
        let dx = grid, dy = 0;
        let food = {x: 280, y: 160};
        let score = 0;
        let gameOver = false;

        window.addEventListener('keydown', e => {
            if (e.code === 'ArrowUp' && dy === 0) { dx = 0; dy = -grid; }
            if (e.code === 'ArrowDown' && dy === 0) { dx = 0; dy = grid; }
            if (e.code === 'ArrowLeft' && dx === 0) { dx = -grid; dy = 0; }
            if (e.code === 'ArrowRight' && dx === 0) { dx = grid; dy = 0; }
            if (gameOver && e.code === 'KeyR') reset();
        });

        function reset() {
            snake = [{x: 160, y: 160}, {x: 140, y: 160}];
            dx = grid; dy = 0; score = 0; gameOver = false;
            document.getElementById('s').innerText = 0;
        }

        function main() {
            if (gameOver) return;
            setTimeout(() => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                const head = {x: snake[0].x + dx, y: snake[0].y + dy};
                
                if (head.x < 0 || head.x >= canvas.width || head.y < 0 || head.y >= canvas.height) gameOver = true;
                for (let part of snake) {
                    if (part.x === head.x && part.y === head.y) gameOver = true;
                }

                if (!gameOver) {
                    snake.unshift(head);
                    if (head.x === food.x && head.y === food.y) {
                        score += 10;
                        document.getElementById('s').innerText = score;
                        food = {
                            x: Math.floor(Math.random() * (canvas.width / grid)) * grid,
                            y: Math.floor(Math.random() * (canvas.height / grid)) * grid,
                        };
                    } else {
                        snake.pop();
                    }
                }

                // Draw Food
                ctx.fillStyle = '#ec4899';
                ctx.fillRect(food.x, food.y, grid - 2, grid - 2);

                // Draw Snake
                snake.forEach((part, i) => {
                    ctx.fillStyle = i === 0 ? '#06b6d4' : '#8b5cf6';
                    ctx.fillRect(part.x, part.y, grid - 2, grid - 2);
                });

                if (gameOver) {
                    ctx.fillStyle = 'rgba(0,0,0,0.8)';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#f43f5e';
                    ctx.font = 'bold 24px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText('GAME OVER', canvas.width / 2, 190);
                    ctx.fillStyle = '#fff';
                    ctx.font = '14px sans-serif';
                    ctx.fillText('Tekan R untuk Main Lagi', canvas.width / 2, 220);
                } else {
                    main();
                }
            }, 100);
        }
        main();
    </script>
</body>
</html>
HTML;
    }

    protected function getSpaceShooterSvg(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="#0f172a"/><polygon points="300,120 360,260 240,260" fill="#06b6d4"/><circle cx="150" cy="100" r="30" fill="#f43f5e"/><circle cx="450" cy="150" r="40" fill="#e11d48"/><line x1="300" y1="120" x2="300" y2="40" stroke="#38bdf8" stroke-width="8"/><text x="300" y="340" font-family="sans-serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle">SPACE SHOOTER 2D</text></svg>';
    }

    protected function getFlappyBirdSvg(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="#38bdf8"/><rect x="180" y="0" width="70" height="150" fill="#10b981"/><rect x="180" y="270" width="70" height="130" fill="#10b981"/><circle cx="340" cy="200" r="35" fill="#f59e0b"/><polygon points="370,195 400,205 370,215" fill="#ef4444"/><text x="300" y="360" font-family="sans-serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle">RETRO FLAPPY FLYER</text></svg>';
    }

    protected function getSnakeSvg(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect width="600" height="400" fill="#090d16"/><rect x="100" y="180" width="40" height="40" rx="8" fill="#06b6d4"/><rect x="150" y="180" width="40" height="40" rx="8" fill="#8b5cf6"/><rect x="200" y="180" width="40" height="40" rx="8" fill="#8b5cf6"/><rect x="250" y="180" width="40" height="40" rx="8" fill="#8b5cf6"/><rect x="350" y="180" width="40" height="40" rx="8" fill="#ec4899"/><text x="300" y="340" font-family="sans-serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle">CYBER SNAKE DX</text></svg>';
    }
}
