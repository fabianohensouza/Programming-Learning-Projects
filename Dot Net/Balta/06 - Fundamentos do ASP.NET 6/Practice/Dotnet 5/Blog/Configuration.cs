namespace Blog
{
    public static class Configuration
    {
        public static string JwtKey = "JhBva=-aDlTrFPZsexvHtaYBpUzhKDS8";
        public static string ApiKeyName = "api_key";
        public static string ApiKey = "Curso=aPi-TrFPZsexvHtaYBpUzhKDS8";
        public static SmtpConfiguration Smtp = new();

        public class SmtpConfiguration
        {
            public string Host { get; set; }
            public int Port { get; set; } = 25;
            public string UserName { get; set; }
            public string Password { get; set; }
        }
    }
}
