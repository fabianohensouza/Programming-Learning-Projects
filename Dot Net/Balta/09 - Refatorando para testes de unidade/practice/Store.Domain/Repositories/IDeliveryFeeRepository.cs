using Store.Domain.Entities;

namespace Store.Domain.Repositories.Interfaces
{
    public interface IDeliveryFeeRepository
    {
        Decimal Get (string zipCode);
    }
}