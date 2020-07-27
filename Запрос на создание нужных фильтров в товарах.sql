SET @iVar = 'Стекло/Зеркало/Пластик/Кристал';
-- SELECT * FROM oc_product_attribute LEFT JOIN oc_product_filter ON oc_product_attribute.product_id = oc_product_filter.product_id WHERE text = @iVar;
SET @AtrId = (SELECT DISTINCT attribute_id FROM oc_product_attribute LEFT JOIN oc_product_filter ON oc_product_attribute.product_id = oc_product_filter.product_id WHERE
        text = @iVar );
 -- AND filter_id IS null);
SET @FilterId = (SELECT filter_id FROM oc_filter_description
                 WHERE name = @iVar);
SELECT @iVar, @AtrId, @FilterId;

 INSERT IGNORE INTO oc_product_filter(product_id, filter_id)
SELECT oc_product_attribute.product_id, @FilterId as filter_id
FROM oc_product_attribute
WHERE attribute_id= @AtrId
  AND text = @iVar;

SELECT * FROM oc_product_filter WHERE filter_id = @FilterId;


 SELECT model FROM oc_product WHERE product_id = 141;
SELECT * FROM oc_product_attribute as _CHECK
                  LEFT JOIN oc_product_filter ON _CHECK.product_id = oc_product_filter.product_id WHERE
 attribute_id = @AtrId AND
 text = @iVar AND
filter_id IS null;

SELECT * FROM oc_product_attribute as New_attribute
                  LEFT JOIN oc_product_filter ON New_attribute.product_id = oc_product_filter.product_id WHERE
    filter_id IS null AND attribute_id <> 27 AND attribute_id <> 28 AND attribute_id <> 29 AND attribute_id <> 31;