(() => {
    const script = document.currentScript;
    const color = script?.dataset?.color || '#00B4FF';
    const siteKey = script?.dataset?.siteKey || 'pk_demo';
    const statusUrl = '/status/demo-status-token';
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const style = document.createElement('style');
    style.textContent = `
        .ch-launcher, .ch-panel, .ch-panel * { box-sizing: border-box; font-family: Instrument Sans, system-ui, sans-serif; }
        .ch-launcher {
            position: fixed; right: 1.25rem; bottom: 1.25rem; z-index: 2147483000;
            border: 1px solid color-mix(in srgb, ${color} 70%, white); border-radius: 9999px;
            background: linear-gradient(180deg, color-mix(in srgb, ${color} 85%, white), ${color});
            color: #031018; padding: 0.95rem 1.25rem; font-weight: 800; font-size: 0.95rem; cursor: pointer;
            letter-spacing: -0.02em; font-family: Space Grotesk, Instrument Sans, system-ui, sans-serif;
            box-shadow: 0 0 0 1px color-mix(in srgb, ${color} 30%, transparent), 0 0 28px color-mix(in srgb, ${color} 45%, transparent);
        }
        .ch-panel {
            position: fixed; right: 1.25rem; bottom: 4.75rem; z-index: 2147483000;
            width: min(360px, calc(100vw - 1.5rem));
            background: rgba(8, 22, 48, 0.94); color: #e8f4ff;
            border: 1px solid color-mix(in srgb, ${color} 45%, transparent); border-radius: 1.1rem;
            box-shadow: 0 0 0 1px color-mix(in srgb, ${color} 15%, transparent), 0 22px 44px rgba(0,0,0,.45), 0 0 40px color-mix(in srgb, ${color} 18%, transparent);
            backdrop-filter: blur(16px); overflow: hidden; display: none;
            ${reduceMotion ? '' : 'animation: ch-in 160ms ease-out;'}
        }
        .ch-panel[data-open="true"] { display: block; }
        @keyframes ch-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        .ch-head {
            background: linear-gradient(90deg, color-mix(in srgb, ${color} 35%, #041126), #041126);
            color: #e8f4ff; padding: 1rem 1.1rem; display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid color-mix(in srgb, ${color} 30%, transparent);
        }
        .ch-head strong { font-family: Space Grotesk, system-ui, sans-serif; font-weight: 800; letter-spacing: -0.03em; font-size: 1.05rem; }
        .ch-close { background: transparent; border: 0; color: inherit; font-size: 1.25rem; cursor: pointer; line-height: 1; }
        .ch-body { padding: 1rem; }
        .ch-field { margin-bottom: 0.75rem; }
        .ch-field label { display: block; font-size: 0.72rem; font-weight: 800; margin-bottom: 0.35rem; color: #7dd3fc; text-transform: uppercase; letter-spacing: 0.06em; }
        .ch-field input, .ch-field textarea {
            width: 100%; border: 1px solid color-mix(in srgb, ${color} 35%, transparent); border-radius: 0.7rem;
            padding: 0.65rem 0.75rem; font: inherit; font-weight: 560; background: rgba(2,12,28,0.9); color: #e8f4ff;
        }
        .ch-field input:focus, .ch-field textarea:focus {
            outline: none; border-color: ${color};
            box-shadow: 0 0 0 3px color-mix(in srgb, ${color} 25%, transparent), 0 0 16px color-mix(in srgb, ${color} 25%, transparent);
        }
        .ch-submit {
            width: 100%; border: 1px solid color-mix(in srgb, ${color} 70%, white); border-radius: 0.75rem;
            background: linear-gradient(180deg, color-mix(in srgb, ${color} 85%, white), ${color});
            color: #031018; font-weight: 800; padding: 0.8rem 1rem; cursor: pointer; letter-spacing: -0.02em;
            font-family: Space Grotesk, Instrument Sans, system-ui, sans-serif;
            box-shadow: 0 0 24px color-mix(in srgb, ${color} 35%, transparent);
        }
        .ch-success { text-align: center; padding: 1.25rem 0.5rem; }
        .ch-success a { color: ${color}; font-weight: 700; }
    `;
    document.head.appendChild(style);

    const launcher = document.createElement('button');
    launcher.type = 'button';
    launcher.className = 'ch-launcher';
    launcher.setAttribute('aria-haspopup', 'dialog');
    launcher.setAttribute('aria-expanded', 'false');
    launcher.textContent = 'Support';

    const panel = document.createElement('div');
    panel.className = 'ch-panel';
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-label', 'Support widget');
    panel.innerHTML = `
        <div class="ch-head">
            <strong>Central Hub Support</strong>
            <button type="button" class="ch-close" aria-label="Close support panel">&times;</button>
        </div>
        <div class="ch-body" data-view="form">
            <form>
                <div class="ch-field"><label for="ch-name">Name</label><input id="ch-name" name="name" required autocomplete="name"></div>
                <div class="ch-field"><label for="ch-email">Email</label><input id="ch-email" name="email" type="email" required autocomplete="email"></div>
                <div class="ch-field"><label for="ch-subject">Subject</label><input id="ch-subject" name="subject" required maxlength="150"></div>
                <div class="ch-field"><label for="ch-message">Message</label><textarea id="ch-message" name="message" rows="4" required maxlength="10000"></textarea></div>
                <button class="ch-submit" type="submit">Send</button>
            </form>
        </div>
    `;

    const open = () => { panel.dataset.open = 'true'; launcher.setAttribute('aria-expanded', 'true'); };
    const close = () => { delete panel.dataset.open; launcher.setAttribute('aria-expanded', 'false'); launcher.focus(); };

    launcher.addEventListener('click', () => panel.dataset.open === 'true' ? close() : open());
    panel.querySelector('.ch-close').addEventListener('click', close);
    panel.querySelector('form').addEventListener('submit', (event) => {
        event.preventDefault();
        panel.querySelector('[data-view="form"]').innerHTML = `
            <div class="ch-success">
                <p><strong>Ticket FRG-00043 created</strong></p>
                <p style="color:#7dd3fc;font-size:0.875rem;margin:0.5rem 0 1rem;">Site key: ${siteKey}</p>
                <a href="${statusUrl}">Track progress</a>
            </div>`;
    });

    document.body.appendChild(panel);
    document.body.appendChild(launcher);
})();
