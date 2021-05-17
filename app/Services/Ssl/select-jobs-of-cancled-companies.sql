select
j.id as jobid
from ssljobs j
inner join campaign_locations l on l.id=j.locationid
where
l.canceled=1
and l.effective_date_of_cancellation <= CURRENT_TIMESTAMP
and j.status <> 6
