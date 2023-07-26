using PaymentContext.Shared.ValueObject;

namespace PaymentContext.Domain.ValueObjects
{
    public class Name : ValueObject
    {
        public Name(string firstname, string lastname)
        {
            Firstname = firstname;
            Lastname = lastname;

            if (string.IsNullOrEmpty(Firstname))
                AddNotification("Name.FirstName", "Nome Inválido");
        }

        public string Firstname { get; private set; }
        public string Lastname { get; private set; }
    }
}
//6Min