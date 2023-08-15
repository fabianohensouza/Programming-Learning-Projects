using PaymentContext.Domain.Services;

namespace PaymentContext.Tests.Mocks
{
    public class FakeEmailService : IEmailService
    {
        public FakeEmailService(string to, string email, string subject, string body)
        {
            To = to;
            Email = email;
            Subject = subject;
            Body = body;
        }

        public string To { get; set; }
        public string Email { get; set; }
        public string Subject { get; set; }
        public string Body { get; set; }
        public void Send(string to, string email, string subject, string body)
        {

        }
    }
}