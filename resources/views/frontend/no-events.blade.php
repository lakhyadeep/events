<x-layouts.microsite
    meta-title="Dibrugarh 24x7 Events - Setup Portal"
    meta-description="Official event portal by Dibrugarh 24x7"
>
    <style>
        .no-events-container {
            min-height: 100vh;
            background: radial-gradient(ellipse at 50% 20%, rgba(183, 13, 24, 0.35) 0%, transparent 65%),
                        radial-gradient(ellipse at 85% 85%, rgba(255, 204, 0, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 15% 85%, rgba(183, 13, 24, 0.2) 0%, transparent 50%),
                        linear-gradient(145deg, #160305 0%, #200408 50%, #110204 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
        }

        .no-events-alpona-bg {
            position: absolute;
            width: 520px;
            height: 520px;
            opacity: 0.05;
            pointer-events: none;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .no-events-alpona-left {
            top: -120px;
            left: -120px;
            transform: rotate(-15deg);
        }

        .no-events-alpona-right {
            bottom: -120px;
            right: -120px;
            transform: rotate(25deg);
        }

        .no-events-card {
            position: relative;
            z-index: 2;
            max-width: 620px;
            width: 100%;
            background: rgba(32, 7, 12, 0.72);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 204, 0, 0.25);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6),
                        0 0 40px rgba(183, 13, 24, 0.25),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem 2.25rem;
            text-align: center;
        }

        .no-events-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 204, 0, 0.12);
            border: 1px solid rgba(255, 204, 0, 0.35);
            color: #ffcc00;
            padding: 0.35rem 1rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .no-events-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.15rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 1rem;
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .no-events-subtitle {
            color: #d1c7bc;
            font-size: 0.95rem;
            line-height: 1.65;
            margin-bottom: 2rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .no-events-icon-glow {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 22px;
            background: linear-gradient(135deg, #b70d18 0%, #ffcc00 100%);
            box-shadow: 0 10px 25px rgba(255, 204, 0, 0.3),
                        0 0 30px rgba(183, 13, 24, 0.4);
            color: #ffffff;
            font-size: 2.25rem;
        }

        .btn-festive-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, #ffcc00 0%, #f59e0b 100%);
            color: #1a0305 !important;
            font-weight: 700;
            font-size: 0.925rem;
            padding: 0.85rem 1.75rem;
            border-radius: 14px;
            border: none;
            box-shadow: 0 8px 24px rgba(255, 204, 0, 0.3);
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-festive-primary:hover {
            background: linear-gradient(135deg, #ffd726 0%, #fbbf24 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 204, 0, 0.45);
            color: #110204 !important;
        }

        .btn-festive-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            background: rgba(255, 255, 255, 0.05);
            color: #f1f5f9 !important;
            font-weight: 600;
            font-size: 0.925rem;
            padding: 0.85rem 1.75rem;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-festive-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 204, 0, 0.4);
            color: #ffcc00 !important;
            transform: translateY(-2px);
        }

        .seeder-tip-box {
            margin-top: 2.25rem;
            padding-top: 1.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .seeder-code {
            display: inline-block;
            background: rgba(0, 0, 0, 0.5);
            border: 1px dashed rgba(255, 204, 0, 0.4);
            color: #fce1a8;
            font-family: monospace;
            font-size: 0.825rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            letter-spacing: 0.02em;
            word-break: break-all;
        }
    </style>

    <div class="no-events-container">
        <!-- Subtle festive alpona accents -->
        <div class="no-events-alpona-bg no-events-alpona-left" style="background-image: url('{{ asset('event-assets/images/alpona1.png') }}');"></div>
        <div class="no-events-alpona-bg no-events-alpona-right" style="background-image: url('{{ asset('event-assets/images/alpona1.png') }}');"></div>

        <div class="no-events-card">
            <!-- Glowing Trophy / Festive Icon -->
            <div class="no-events-icon-glow">
                <i class="bi bi-calendar-event"></i>
            </div>

            <!-- Portal Badge -->
            <div class="no-events-badge-pill">
                <i class="bi bi-stars"></i>
                <span>Dibrugarh 24x7 Events</span>
            </div>

            <!-- Headline -->
            <h1 class="no-events-title">
                No Active Event Configured Yet
            </h1>

            <!-- Description -->
            <p class="no-events-subtitle">
                The festive microsite engine is live and ready. To display the festive competition and voting portal, please sign in to the admin panel to activate an event or run the database seeder.
            </p>

            <!-- Action Buttons -->
            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3">
                <a href="{{ url('/admin') }}" class="btn-festive-primary">
                    <i class="bi {{ \App\Models\User::doesntExist() ? 'bi-shield-lock-fill' : 'bi-speedometer2' }}"></i>
                    <span>{{ \App\Models\User::doesntExist() ? 'Initial Setup: Create Superadmin' : 'Go to Admin Dashboard' }}</span>
                </a>

                <a href="{{ url('/admin/events') }}" class="btn-festive-secondary">
                    <i class="bi bi-gear-fill"></i>
                    <span>Manage Events</span>
                </a>
            </div>

            <!-- Quick Seeder Instruction -->
            <div class="seeder-tip-box">
                <small class="text-secondary" style="color: #94a3b8 !important;">
                    <i class="bi bi-terminal me-1"></i> Quick setup for local development:
                </small>
                <div>
                    <code class="seeder-code">php artisan db:seed --class=DemoEventSeeder</code>
                </div>
            </div>
        </div>
    </div>
</x-layouts.microsite>
