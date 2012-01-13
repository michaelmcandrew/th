
<div class='crm-block crm-form-block'>
<table class="form-layout-compressed">
	<h2>Approve member</h2>
	<p>Click 'Reject application' to send an email to this user to inform that their membership application has been rejected.  You can optionally add extra text to the rejection email in the box below.</p>
	<p>{$message}</p>
	<tr><td>{$form.template.label}</td><td>{$form.template.html}</td></tr>
	<tr><td>{$form.extra_text.label}</td><td>{$form.extra_text.html}</td></tr>
	<tr><td> </td><td>{include file="CRM/common/formButtons.tpl" location="bottom"}</td></tr>
</table>
</div>
