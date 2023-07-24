namespace PaymentContext.Domain.ValueObjects
{
    public class Name
    {
        public Name(string firstname, string lastname)
        {
            Firstname = firstname;
            Lastname = lastname;
        }

        public string Firstname { get; private set; }
        public string Lastname { get; private set; }
    }
}