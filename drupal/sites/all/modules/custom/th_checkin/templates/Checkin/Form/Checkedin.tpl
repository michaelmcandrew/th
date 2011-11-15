<h2>People checked in at TechHub London</h2>
<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>check out</th>
</tr>
{foreach from=$visitors item=visitor}
<tr class={cycle values="odd-row,even-row"}>
<td>{$visitor.display_name}</td>
<td>{$visitor.email}</td>
<td><a href="/civicrm/checkin/checkout?cid={$visitor.contact_id}">checkout</td>
</tr>
{/foreach}
</table>
<div class='crm-block crm-form-block'>
<table class="form-layout-compressed">
	<h2>Check in visitor</h2>
	<tr><td>{$form.first_name.label}</td><td>{$form.first_name.html}</td></tr>
	<tr><td>{$form.last_name.label}</td><td>{$form.last_name.html}</td></tr>
	<tr><td>{$form.email.label}</td><td>{$form.email.html}</td></tr>
	<tr><td>{$form.organization.label}</td><td>{$form.organization.html}</td></tr>
	<tr><td>{$form.visiting_contact_id.label}</td><td>{$form.visiting_contact_id.html}</td></tr>
	<tr><td> </td><td>{include file="CRM/common/formButtons.tpl" location="bottom"}</td></tr>
</table>
</div>
