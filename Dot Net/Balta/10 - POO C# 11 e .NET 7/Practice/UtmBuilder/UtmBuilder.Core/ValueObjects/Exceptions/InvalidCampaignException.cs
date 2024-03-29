namespace UtmBuilder.Core.ValueObjects.Exceptions
{
    public class InvalidCampaignException : Exception
    {
        private const string DefaultErrorMessage = "Invalid UTM parameters";

        public InvalidCampaignException(string message = DefaultErrorMessage)
            : base(message)
        {

        }

        public static void ThrowIfInvalid(
            string item,
            string message = DefaultErrorMessage)
        {
            if (string.IsNullOrEmpty(item) || item == " ")
                throw new InvalidCampaignException(message);
        }
    }
}