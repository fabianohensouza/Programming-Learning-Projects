
namespace PaymentContext.Domain.Entities
{
    public class Student
    {
        public string Firstname { get; set; }
        public string Lastname { get; set; }
        public string Document { get; set; }
        public string Email { get; set; }
        public string Address { get; set; }
        public List<Subscription> Subscriptions { get; set; }
    }
}