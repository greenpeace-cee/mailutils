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
          <p><b>{$startSmartyEscapeWord}</b> - start word</p>
          <p><b>{$endSmartyEscapeWord}</b> - end word</p>
          <p>Example:</p>
          <p>
            <b>{$startSmartyEscapeWord}</b>
            {literal}
              <i>{if $variable}</i>
            {/literal}
            <b>{$endSmartyEscapeWord}</b>

            <span>&lt;p&gt;&lt;i&gt;variable is exist&nbsp;&lt;/i&gt;&lt;/p&gt; </span>

            <b>{$startSmartyEscapeWord}</b>
            {literal}
              <i>{else}</i>
            {/literal}
            <b>{$endSmartyEscapeWord}</b>

            <span>&lt;p&gt;&lt;i&gt;variable doesn't exist&nbsp;&lt;/i&gt;&lt;/p&gt;</span>

            <b>{$startSmartyEscapeWord}</b>
              {literal}
                <i>{/if}</i>
              {/literal}
            <b>{$endSmartyEscapeWord}</b>
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
