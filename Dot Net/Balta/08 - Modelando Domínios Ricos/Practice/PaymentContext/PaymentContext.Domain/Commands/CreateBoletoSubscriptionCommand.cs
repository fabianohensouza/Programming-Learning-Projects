namespace PaymentContext.Domain.Commands
{
    public class CreateBoletoSubscriptionCommand : CreateSubscriptionCommand
    {
        public string BarCode { get; set; }
        public string BoletoNumber { get; set; }
    }
}