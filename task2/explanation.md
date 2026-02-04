
## Changed made to the code

i refactored the method by eager loading the required relationships to avoid n+1 queries i also moved calculations like totals and item counts to the database using withcount and withsum this reduces the number of queries improves performance and makes the code easier to understand 




## Further improvements

further improvements could include pagination database indexes
, loading only necessary completed orders, load only needed columns, returning collection
