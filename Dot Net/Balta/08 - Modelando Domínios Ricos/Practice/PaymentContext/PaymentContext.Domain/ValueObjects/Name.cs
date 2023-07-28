using Flunt.Validations;
using PaymentContext.Shared.ValueObject;

namespace PaymentContext.Domain.ValueObjects
{
    public class Name : ValueObject
    {
        public Name(string firstname, string lastname)
        {
            Firstname = firstname;
            Lastname = lastname;

            AddNotifications(new Contract()
                .Requires()
                .HasMinLen(Firstname, 3, "Name.FirstName", "O Nome deve conter pelo menos 3 letras")
                .HasMinLen(Lastname, 3, "Name.LastName", "O Sobrenome deve conter pelo menos 3 letras")
            );
        }

        public string Firstname { get; private set; }
        public string Lastname { get; private set; }
    }
}
//6Min