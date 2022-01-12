<div class="crm-submit-buttons">
  <a href="{crmURL p="civicrm/mailutils/template/add"}" class="button"><span><i class="crm-i fa-plus-circle" aria-hidden="true"></i> Add Template</span></a>
</div>
<table cellpadding="0" cellspacing="0" border="0">
  <tr class="columnheader">
    <th>{ts}Name{/ts}</th>
    <th>{ts}Category{/ts}</th>
    <th colspan="2">{ts}Message{/ts}</th>
  </tr>
    {foreach from=$mailutilsTemplates item=row}
      <tr class="crm-entity {cycle values="odd-row,even-row"}">
        <td>{$row.name|escape}</td>
        <td>{$row.category|escape}</td>
        <td>{$row.message|escape|nl2br}</td>
        <td>
          <span>
            <a class="action-item crm-hover-button" href="{crmURL p="civicrm/mailutils/template/add" q="reset=1&id=`$row.id`"}">Edit</a>
          </span>
        </td>
      </tr>
    {/foreach}
</table>
