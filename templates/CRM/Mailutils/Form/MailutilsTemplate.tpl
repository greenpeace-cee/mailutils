<div class="crm-form-block">
  <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <div>
    {foreach from=$elementNames item=elementName}
      <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
      </div>
    {/foreach}

    <div class="crm-section">
      <div class="label"></div>
      <div class="content">
        <div class="help">
          <p>
            To use Smarty in the template.<br>
            You need to escape smarty code.<br>
            To do that use those word before/after smarty code:
          </p>
          <p>{$startSmartyEscapeWord} - start word</p>
          <p>{$endSmartyEscapeWord} - end word</p>
          <p>Example:</p>
          <p>
            {$startSmartyEscapeWord}
            {literal}
              {if $variable}
            {/literal}
            {$endSmartyEscapeWord}

            <span>variable is exist</span>

            {$startSmartyEscapeWord}
            {literal}
              {else}
            {/literal}
            {$endSmartyEscapeWord}

            variable doesn't exist

            {$startSmartyEscapeWord}
              {literal}
                {/if}
              {/literal}
            {$endSmartyEscapeWord}
          </p>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>

  <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
