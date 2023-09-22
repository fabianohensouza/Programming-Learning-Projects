using Store.Domain.Entities;

namespace Store.Domain.Repositories
{
    public interface IDeliveryFeeRepository
    {
        Decimal Get (string zipCode);
    }
}