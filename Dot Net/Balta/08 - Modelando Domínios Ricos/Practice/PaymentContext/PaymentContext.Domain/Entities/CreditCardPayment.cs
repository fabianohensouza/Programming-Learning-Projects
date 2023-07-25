
using PaymentContext.Domain.ValueObjects;

namespace PaymentContext.Domain.Entities
{
    public class CreditCardPayment : Payment
    {
        public CreditCardPayment(
            string cardHolderNamer,
            string cardNumber,
            string lastTransactionNumber,
            DateTime paidDate,
            DateTime expireDate,
            decimal total,
            decimal totalPaid,
            string payer,
            Document document,
            string owner,
            Address address,
            Email email) : base(
                paidDate,
                expireDate,
                total,
                totalPaid,
                payer,
                document,
                owner,
                address,
                email)
        {
            CardHolderNamer = cardHolderNamer;
            CardNumber = cardNumber;
            LastTransactionNumber = lastTransactionNumber;
        }

        public string CardHolderNamer { get; private set; }
        public string CardNumber { get; private set; } //Last 4 credit card digits
        public string LastTransactionNumber { get; private set; }
    }
}