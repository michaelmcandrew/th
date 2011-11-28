
<div class='crm-block crm-form-block'>
<table class="form-layout-compressed">
	<h2>Approve member</h2>
	<p>Send an email to this user with details of how to pay for their TechHub application.</p>
	<p>{$message}</p>
	<tr><td>{$form.template.label}</td><td>{$form.template.html}</td></tr>
	<tr><td>{$form.extra_text.label}</td><td>{$form.extra_text.html}</td></tr>
	<tr><td> </td><td>{include file="CRM/common/formButtons.tpl" location="bottom"}</td></tr>
</table>
</div>
