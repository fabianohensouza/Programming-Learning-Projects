using Flunt.Notifications;
using Flunt.Validations;
using PaymentContext.Domain.Enuns;
using PaymentContext.Domain.ValueObjects;
using PaymentContext.Shared.Commands;

namespace PaymentContext.Domain.Commands
{
    public class CreateSubscriptionCommand : Notifiable, ICommand
    {
        public string Firstname { get; set; }
        public string Lastname { get; set; }
        public string Document { get; set; }
        public string Email { get; set; }
        public string PaymentNumber { get; set; }
        public DateTime PaidDate { get; set; }
        public DateTime ExpireDate { get; set; }
        public decimal Total { get; set; }
        public decimal TotalPaid { get; set; }
        public string Payer { get; set; }
        public string PayerDocument { get; set; }
        public EDocumentType PayerDocumentType { get; set; }
        public string PayerEmail { get; set; }
        public string Owner { get; set; }
        public string Street { get; set; }
        public string Number { get; set; }
        public string City { get; set; }
        public string State { get; set; }
        public string Country { get; set; }
        public string ZipCode { get; set; }
        public Email EmailAddres { get { return new Email(Email); } }
        public Address Address
        {
            get
            {
                return new Address(
            Street,
            Number,
            City,
            State,
            Country,
            ZipCode);
            }
        }

        public virtual void Validate()
        {
            AddNotifications(new Contract()
                .Requires()
                .HasMinLen(Firstname, 3, "Name.FirstName", "O Nome deve conter pelo menos 3 letras")
                .HasMinLen(Lastname, 3, "Name.LastName", "O Sobrenome deve conter pelo menos 3 letras")
            );
        }
    }
}