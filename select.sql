select valid_etime,id from t
	where id > 10 or (price+2 = 100) and (valid_etime/2 > "2020-10-01" or id = 10 and price < 10)
	order by id,price desc
	limit 0,10