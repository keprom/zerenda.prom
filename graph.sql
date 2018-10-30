-- View: industry.graph

-- DROP VIEW industry.graph;

CREATE OR REPLACE VIEW industry.graph AS 
 SELECT "7-re".firm_id, COALESCE("7-re".itogo_kvt, 0::numeric) AS itogo_kvt, period.name AS period_name, period.begin_date AS period_begin_date, period.id AS period_id
   FROM industry."7-re"
   LEFT JOIN industry.period ON period.id = "7-re".period_id
  ORDER BY "7-re".period_id;

ALTER TABLE industry.graph OWNER TO postgres;
GRANT ALL ON TABLE industry.graph TO postgres;
GRANT ALL ON TABLE industry.graph TO public;
