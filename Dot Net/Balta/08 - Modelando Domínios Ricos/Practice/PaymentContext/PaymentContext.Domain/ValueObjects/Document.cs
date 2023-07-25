using PaymentContext.Domain.Enuns;
using PaymentContext.Shared.ValueObject;

namespace PaymentContext.Domain.ValueObjects
{
    public class Document : ValueObject
    {
        public Document(string number, EDocumentType tYpe)
        {
            Number = number;
            TYpe = tYpe;
        }

        public string Number { get; private set; }
        public EDocumentType TYpe { get; private set; }
    }
}