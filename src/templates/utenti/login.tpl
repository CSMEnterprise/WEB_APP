<div style="min-height: 75vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
    <div class="card" style="max-width: 420px; width: 100%; border-radius: 28px; padding: 48px 40px; box-shadow: 0 24px 48px rgba(0,0,0,0.6), 0 0 0 1px rgba(124,58,237,0.15); background: var(--bg-card); position: relative; overflow: hidden;">
        <div style="position: absolute; top: -80px; right: -80px; width: 200px; height: 200px; background: var(--accent); border-radius: 50%; filter: blur(70px); opacity: 0.15; pointer-events: none;"></div>
        <div style="position: absolute; bottom: -80px; left: -80px; width: 200px; height: 200px; background: #ec4899; border-radius: 50%; filter: blur(70px); opacity: 0.1; pointer-events: none;"></div>

        <div style="text-align: center; margin-bottom: 40px; position: relative; z-index: 1;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 20px; background: rgba(124,58,237,0.1); color: var(--accent); margin-bottom: 24px; transform: rotate(-5deg); box-shadow: inset 0 0 0 1px rgba(124,58,237,0.2);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            </div>
            <h1 style="font-size: 34px; font-weight: 800; letter-spacing: -0.04em; margin-bottom: 8px;">Bentornato</h1>
            <p style="color: var(--muted); font-size: 15px; font-weight: 500;">Accedi per continuare in NerdVault</p>
        </div>

        {if $resetOk|default:false}
            <div class="alert alert-success" style="border-radius:12px;text-align:center;position:relative;z-index:1;margin-bottom:24px;">
                Password aggiornata! Ora puoi accedere.
            </div>
        {/if}

        {if $errore|default:''}
            {if $isEmailNonVerificata|default:false}
                <div class="alert alert-error" style="border-radius:12px;position:relative;z-index:1;margin-bottom:16px;">
                    <strong>Email non verificata.</strong><br>
                    Controlla la tua casella oppure
                    <a href="index.php?route=verifica-email-attesa&amp;email={$emailNonVerificataUrl}"
                       style="color:#fca5a5;font-weight:700;">richiedi un nuovo link</a>.
                </div>
            {else}
                <div class="alert alert-error" style="border-radius: 12px; font-weight: 600; text-align: center; position: relative; z-index: 1; border: none; background: rgba(239,68,68,0.15); color: #fca5a5; padding: 14px; margin-bottom: 24px;">{$errore}</div>
            {/if}
        {/if}

        <form method="post" action="index.php" style="position: relative; z-index: 1;">
            <input type="hidden" name="route" value="login-post">

            <div style="margin-bottom: 24px;">
                <label for="email" style="font-size: 12px; font-weight: 700; color: var(--muted); letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 10px; display: block;">Email</label>
                <input type="email" id="email" name="email" required style="width: 100%; max-width: none; background: rgba(0,0,0,0.25); border: 2px solid transparent; border-radius: 16px; padding: 16px 20px; font-size: 15px; color: #fff; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); margin: 0;" onfocus="this.style.borderColor='var(--accent)'; this.style.background='rgba(0,0,0,0.4)';" onblur="this.style.borderColor='transparent'; this.style.background='rgba(0,0,0,0.25)';">
            </div>

            <div style="margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 10px;">
                    <label for="loginPassword" style="font-size: 12px; font-weight: 700; color: var(--muted); letter-spacing: 0.05em; text-transform: uppercase; margin: 0;">Password</label>
                    <a href="index.php?route=recupero-password" style="font-size: 13px; color: var(--accent); text-decoration: none; font-weight: 600; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Dimenticata?</a>
                </div>
                <div style="position: relative;">
                    <input type="password" id="loginPassword" name="password" required style="width: 100%; max-width: none; background: rgba(0,0,0,0.25); border: 2px solid transparent; border-radius: 16px; padding: 16px 80px 16px 20px; font-size: 15px; color: #fff; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1); margin: 0;" onfocus="this.style.borderColor='var(--accent)'; this.style.background='rgba(0,0,0,0.4)';" onblur="this.style.borderColor='transparent'; this.style.background='rgba(0,0,0,0.25)';">
                    <button type="button" onclick="togglePasswordVisibility('loginPassword', this)" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: var(--muted); font-size: 13px; font-weight: 700; cursor: pointer; padding: 8px 12px; border-radius: 10px; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">Mostra</button>
                </div>
            </div>

            <button type="submit" style="width: 100%; background: var(--accent); color: white; border: none; border-radius: 16px; padding: 18px; font-size: 16px; font-weight: 700; letter-spacing: 0.02em; cursor: pointer; transition: all 0.2s; box-shadow: 0 8px 24px rgba(124,58,237,0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 28px rgba(124,58,237,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 8px 24px rgba(124,58,237,0.3)';">
                Accedi ora
            </button>
        </form>

        <div style="text-align: center; margin-top: 32px; position: relative; z-index: 1;">
            <p style="color: var(--muted); font-size: 14px;">Nuovo esploratore? <a href="index.php?route=register" style="color: white; font-weight: 700; text-decoration: none; border-bottom: 2px solid var(--accent); padding-bottom: 2px; transition: all 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='white'">Crea un account</a></p>
        </div>
    </div>
</div>
