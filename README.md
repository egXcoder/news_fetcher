## News Fetcher



DataRequester

New York .. request new york
news api .. request news api
guardian .. request guardian


api url
filters
auth token 
data returned
save into database


Saver


Commands to launch fetcher


Question is: How Frequent would requester runs?
 run in only when possible

[Design Phase 1]

[SRP Respected]
- DataRequester (N classes) Should only bring data from api
- DataRequestValidator (N classes) should see if possible to bring data from api or we exceeded quota
- DataMapper (N classes) should take returned data from api and map it to our internal database mapping
- DataSaver (1 class) Should  save data returned from api into database
- Fetcher (1 class) will have one method to use DataRequester + DataMapper + DataSaver .. [Facade Pattern]
- Command (1 class) with argument of which NewsApi should be used


[Design Phase 2]

instead of building N classes for DataRequester + DataMapper + DataRequest Validator .. so that whenever we have a new api to consume we have to add 3 classes .. we can combine these operations into one class and think of new provider in modular way to encapsulate what vary between NewsProviders [Strategy Pattern]


- NewsProvider (N classes) knows how to fetch + map + isAbleToFetch
- DataSaver (1 class) Should  save data returned from api into database
- Fetcher (1 class) will have one method to use fetch + map + DataSaver .. [Facade Pattern]
- Command (1 class) with argument of which NewsApi should be used



[Design Phase 3]

i feel its too much for NewsProvider to do 3 things, i can accept it to fetch + map, but isAbleToFetch main idea one of the api can allow fetching every hour while another api can fetch daily


- NewsProvider (N classes) knows how to fetch + map [strategy]
- FetchPolicy (1 class) will check should fetch + record last run 
- DataSaver (1 class) Should  save data returned from api into database
- Fetcher (1 class) will have one method to use NewsProvider + FetchPolicy + DataSaver .. [Facade Pattern]
- Command (1 class) with argument of which NewsApi should be used