{crmAPI var="Memberships" entity="Membership" action="get" version="3" contact_id=$contactId}
<div class="contact_details" id="contact_panel_wrap"><div class="contact_panel">
<table><tr><td class='label'>Membership info</td>
{if $Memberships.count}
{crmAPI var="MembershipStatuses" entity="MembershipStatus" action="get" version="3" }
{foreach from=$Memberships.values item=Membership}
<td class='value' style="background-color: 
{if $Membership.status_id eq 1 || $Membership.status_id eq 2}
    #cef2ce
{elseif $Membership.status_id eq 3}
    #f2f2ce
{else}
    #f2cece
{/if};">
{$Membership.status_id|crmApiLookup:$MembershipStatuses} {$Membership.membership_name} membership (end date {$Membership.end_date})</td>
{/foreach}
{else}
<td class='value' style="border: 1px dashed #ff8080; background-color:white">
No membership record</td>
{/if}
</table>
</div></div>
