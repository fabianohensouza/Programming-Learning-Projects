namespace PaymentContext.Domain.Commands
{
    public class CreateCreditCardSubscriptionCommand : CreateSubscriptionCommand
    {
        public string CardHolderNamer { get; set; }
        public string CardNumber { get; set; } //Last 4 credit card digits
        public string LastTransactionNumber { get; set; }
    }
}