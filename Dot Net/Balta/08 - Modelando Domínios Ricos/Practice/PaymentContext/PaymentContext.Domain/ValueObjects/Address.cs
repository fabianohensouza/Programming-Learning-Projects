using Flunt.Validations;
using PaymentContext.Shared.ValueObject;

namespace PaymentContext.Domain.ValueObjects
{
    public class Address : ValueObject
    {
        public Address(string street, string number, string city, string state, string country, string zipCode)
        {
            Street = street;
            Number = number;
            City = city;
            State = state;
            Country = country;
            ZipCode = zipCode;

            AddNotifications(new Contract()
                .Requires()
                .HasMinLen(Street, 2, "Address.Street", "Rua inválida")
                .HasMinLen(Number, 1, "Address.Number", "Número inválido")
                .HasMinLen(State, 2, "Address.State", "Estado inválido")
            );
        }

        public string Street { get; private set; }
        public string Number { get; private set; }
        public string City { get; private set; }
        public string State { get; private set; }
        public string Country { get; private set; }
        public string ZipCode { get; private set; }
    }
}