<div class="crm-submit-buttons">
  <a href="{crmURL p="civicrm/mailutils/template/add"}" class="button"><span><i class="crm-i fa-plus-circle" aria-hidden="true"></i> Add Template</span></a>
</div>
<table cellpadding="0" cellspacing="0" border="0">
  <tr class="columnheader">
    <th>{ts}Name{/ts}</th>
    <th>{ts}Template Category{/ts}</th>
    <th>{ts}Support Case Category{/ts}</th>
    <th colspan="2">{ts}Message{/ts}</th>
  </tr>
    {foreach from=$mailutilsTemplates item=row}
      <tr class="crm-entity {cycle values="odd-row,even-row"}">
        <td>{$row.name|escape}</td>
        <td>{$row.template_category|escape}</td>
        <td>{if $row.support_case_category}{$row.support_case_category|escape}{else}All{/if}</td>
        <td>{$row.message|purify}</td>
        <td>
          <span>
            <a class="action-item crm-hover-button" href="{crmURL p="civicrm/mailutils/template/add" q="reset=1&id=`$row.id`"}">Edit</a>
          </span>
        </td>
      </tr>
    {/foreach}
</table>
