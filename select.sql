select *,valid_etime,id from t
where  name = "张三"
order by id desc,name asc
limit 10,20