{crmAPI var="MembershipStatuses" entity="MembershipStatus" action="get" version="3" }

{crmAPI var="Memberships" entity="Membership" action="get" version="3"  contact_id=$contactId}
{foreach from=$Memberships.values item=Membership}
<div class="contact_details"><div class="contact_panel">
<table><tr><td class='label'>Membership info</td><td class='value' style="background-color: 
{if $Membership.status_id eq 1 || $Membership.status_id eq 2}
    #cef2ce
{elseif $Membership.status_id eq 3}
    #f2f2ce
{else}
    #f2cece
{/if};">
{$Membership.status_id|crmApiLookup:$MembershipStatuses} {$Membership.membership_name} membership (end date {$Membership.end_date})
</table>
</div></div>
{/foreach}

