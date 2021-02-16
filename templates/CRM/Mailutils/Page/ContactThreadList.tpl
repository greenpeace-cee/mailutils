<table>
  <thead>
    <tr>
      <th>
        Sender
      </th>
      <th>
        Subject
      </th>
      <th>
        Date
      </th>
    </tr>
  </thead>
  <tbody>
{foreach from=$threads item=thread}
  {foreach from=$thread.mailutils_messages item=message}
    <tr>
      <td>{$message.from_name|escape}</td>
      <td>{$message.subject|escape}</td>
      <td>Dec 5th, 20:24</td>
    </tr>
  {/foreach}
  <tr>
    <td colspan="3"><hr /></td>
  </tr>
{/foreach}
  </tbody>
</table>
