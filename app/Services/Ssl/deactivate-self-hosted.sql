update
ssljobs
set status=6
where locationid in (select id from campaign_locations where agentId3=137)
