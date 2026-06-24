<div class="pg-phone-field js-phone-field">
    <select class="pg-select pg-phone-prefix" id="telefono_paese" name="telefono_paese" autocomplete="tel-country-code" data-phone-prefix required aria-label="Paese e prefisso telefonico">
        <option value="IT" data-prefix="+39" data-min="6" data-max="10" {if ($post.telefono_paese|default:'IT') == 'IT'}selected{/if}>Italia (+39)</option>
        <option value="FR" data-prefix="+33" data-min="9" data-max="9" {if ($post.telefono_paese|default:'') == 'FR'}selected{/if}>Francia (+33)</option>
        <option value="DE" data-prefix="+49" data-min="5" data-max="11" {if ($post.telefono_paese|default:'') == 'DE'}selected{/if}>Germania (+49)</option>
        <option value="ES" data-prefix="+34" data-min="9" data-max="9" {if ($post.telefono_paese|default:'') == 'ES'}selected{/if}>Spagna (+34)</option>
        <option value="GB" data-prefix="+44" data-min="7" data-max="10" {if ($post.telefono_paese|default:'') == 'GB'}selected{/if}>Regno Unito (+44)</option>
        <option value="US" data-prefix="+1" data-min="10" data-max="10" {if ($post.telefono_paese|default:'') == 'US'}selected{/if}>Stati Uniti (+1)</option>
        <option value="CA" data-prefix="+1" data-min="10" data-max="10" {if ($post.telefono_paese|default:'') == 'CA'}selected{/if}>Canada (+1)</option>
        <option value="CH" data-prefix="+41" data-min="9" data-max="9" {if ($post.telefono_paese|default:'') == 'CH'}selected{/if}>Svizzera (+41)</option>
        <option value="AT" data-prefix="+43" data-min="4" data-max="13" {if ($post.telefono_paese|default:'') == 'AT'}selected{/if}>Austria (+43)</option>
        <option value="BE" data-prefix="+32" data-min="8" data-max="9" {if ($post.telefono_paese|default:'') == 'BE'}selected{/if}>Belgio (+32)</option>
        <option value="NL" data-prefix="+31" data-min="9" data-max="9" {if ($post.telefono_paese|default:'') == 'NL'}selected{/if}>Paesi Bassi (+31)</option>
        <option value="PT" data-prefix="+351" data-min="9" data-max="9" {if ($post.telefono_paese|default:'') == 'PT'}selected{/if}>Portogallo (+351)</option>
        <option value="PL" data-prefix="+48" data-min="9" data-max="9" {if ($post.telefono_paese|default:'') == 'PL'}selected{/if}>Polonia (+48)</option>
        <option value="RO" data-prefix="+40" data-min="9" data-max="9" {if ($post.telefono_paese|default:'') == 'RO'}selected{/if}>Romania (+40)</option>
        <option value="AL" data-prefix="+355" data-min="8" data-max="9" {if ($post.telefono_paese|default:'') == 'AL'}selected{/if}>Albania (+355)</option>
        <option value="BR" data-prefix="+55" data-min="10" data-max="11" {if ($post.telefono_paese|default:'') == 'BR'}selected{/if}>Brasile (+55)</option>
    </select>
    <input class="pg-input pg-phone-number" type="tel" id="telefono" name="telefono_numero" value="{$post.telefono_numero|default:''}" placeholder="Numero" inputmode="numeric" autocomplete="tel-national" pattern="[0-9]{literal}{4,13}{/literal}" minlength="4" maxlength="13" data-phone-number required>
</div>
