namespace PaymentContext.Domain.Commands
{
    public class CreatePayPalSubscriptionCommand : CreateSubscriptionCommand
    {
        public string TransactionCode { get; set; }
    }
}