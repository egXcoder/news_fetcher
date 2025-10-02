## News Fetcher


Pulling News From Various Different News APIs


## Ingestion
- NewsProvider (N classes) knows how to fetch + map ...  [strategy pattern]
- FetchContext (1 class) DTO which will hold last_updated_at + page
- FetchResult (1 class) DTO which will hold data returned from NewProvider + nextFetchContext
- FetchContextRepository (1 class) to record fetchcontext in database and to retrieve
- DataSaver (1 class) Should  save data returned from api into database
- Fetcher (1 class) will have one method to use NewsProvider + FetchPolicy + DataSaver .. [Facade Pattern]
- ThrottledFetcher (1 class) will wrap Fetcher to throttle requests to api
- Command (1 class) with argument of which NewsApi should be used


## Notice
- No need to create a class for FetchContext, since it will already live as DTO (then no confusion)