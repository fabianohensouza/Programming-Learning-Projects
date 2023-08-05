using PaymentContext.Domain.Commands;

namespace PaymentContext.Tests.Commands
{
    [TestClass]
    public class CreateBoletoSubscriptionCommandTests
    {
        [TestMethod]
        public void ShouldReturnErrorWhenNameIsInvalid()
        {
            var command = new CreateBoletoSubscriptionCommand
            {
                Firstname = "",
                Lastname = ""
            };

            command.Validate();
            Assert.AreEqual(false, command.Valid);
        }
    }
}