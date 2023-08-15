using PaymentContext.Domain.Commands;
using PaymentContext.Domain.Enuns;
using PaymentContext.Domain.Handlers;
using PaymentContext.Domain.ValueObjects;
using PaymentContext.Tests.Mocks;

namespace PaymentContext.Tests.Handlers
{
    [TestClass]
    public class SubscriptionHandlerTests
    {
        [TestMethod]
        public void ShouldReturnaErrorWhenDocumentExists()
        {
            var handler = new SubscriptionHandler(
                new FakeStudentRepository(),
                new FakeEmailService(
                    "teste@teste.com",
                    "balta@balta.io",
                    "Email Subject",
                    "Email Content Body"
                ));

            var command = new CreateBoletoSubscriptionCommand();
            command.Firstname = "Jose";
            command.Lastname = "Augusto";
            command.Document = "77788899912";
            command.Email = "teste@teste.com";
            command.PaymentNumber = "789456";
            command.PaidDate = DateTime.Now;
            command.ExpireDate = DateTime.Now.AddMonths(1);
            command.Total = 256.99m;
            command.TotalPaid = 256.99m;
            command.Payer = "Jose";
            command.PayerDocument = "77788899912";
            command.PayerDocumentType = EDocumentType.CPF;
            command.PayerEmail = "teste@teste.com";
            command.Street = "Rua A";
            command.Number = "123";
            command.City = "BH";
            command.State = "MG";
            command.Country = "BR";
            command.ZipCode = "12345689";
            command.BarCode = "65498751362169654657498794";
            command.BoletoNumber = "65498751362169654657498794";
        }
    }
}//10Min