using Microsoft.VisualStudio.TestTools.UnitTesting;
using PaymentContext.Domain.Entities;
using PaymentContext.Domain.Enuns;
using PaymentContext.Domain.ValueObjects;

namespace PaymentContext.Tests.ValueObjects
{
    [TestClass]
    public class StudentTests
    {
        //Red, Green, Refactor

        private readonly Name _name;
        private readonly Document _document;
        private readonly Email _email;
        private readonly Student _student;
        private readonly Address _address;
        private readonly Subscription _subscription;
        private readonly PayPalPayment _payment;

        public StudentTests()
        {
            _name = new Name("Bruce", "Wayne");
            _document = new Document("01987654321", EDocumentType.CPF);
            _email = new Email("batman@dc.com");
            _address = new Address("Rua A", "55", "Gothan", "Gothan State", "US", "7845698858");
            _student = new Student(_name, _document, _email);
            _subscription = new Subscription(null);
            _payment = new PayPalPayment(
                "12345678",
                DateTime.Now,
                DateTime.Now.AddDays(5),
                10,
                10,
                "Wayne Corp",
                _document,
                _name.FullName,
                _address,
                _email);

        }
        [TestMethod]
        public void ShouldReturnErrorWhenHadActiveSubscription()
        {
            _subscription.AddPayment(_payment);
            _student.AddSubscription(_subscription);
            _student.AddSubscription(_subscription);

            Assert.IsTrue(_student.Invalid);
        }

        [TestMethod]
        public void ShouldReturnErrorWhenSubscriptionHasNoPayment()
        {
            _student.AddSubscription(_subscription);

            Assert.IsTrue(_student.Invalid);
        }

        [TestMethod]
        public void ShouldReturnSuccessWhenAddSubscription()
        {
            _subscription.AddPayment(_payment);
            _student.AddSubscription(_subscription);

            Assert.IsTrue(_student.Valid);
        }
    }
}

//16Min