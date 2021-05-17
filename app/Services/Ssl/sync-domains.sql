update
ssljobs s
inner join campaign_locations l on s.locationid=l.id
set
s.domain = l.domain
