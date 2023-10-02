namespace UtmBuilder.Core.ValueObjects
{
    public class Url : ValueObject
    {
        private const string UrlRegexPatterna =
            @"^(http|https):(\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$";
        /// <summary>
        /// Create a new URL
        /// </summary>
        /// <param name="address">Address of URL (Website link)</param>
        public Url(string address)
        {
            Address = address;
        }

        /// <summary>
        /// Address of URL (Website link)
        /// </summary>
        public string Address { get; }
    }
}
