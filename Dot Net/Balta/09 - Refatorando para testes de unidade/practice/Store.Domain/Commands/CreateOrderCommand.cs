using System.Runtime.CompilerServices;
using Flunt.Notifications;
using Flunt.Validations;
using Store.Domain.Commands.Interfaces;

namespace Store.Domain.Commands
{
    public class CreateOrderCommand : Notifiable, ICommand
    {
        public CreateOrderCommand() 
        { 
            Items = new List<CreateOrderItemCommand>();
        }

        public CreateOrderCommand(string customer, string zipCode, string promoCode, IList<CreateOrderItemCommand> items)
        {
            Customer = customer;
            ZipCode = zipCode;
            PromoCode = promoCode;
            Items = items;
        }

        public string Customer { get; set; }
        public string ZipCode { get; set; }
        public string PromoCode { get; set; }
        public IList<CreateOrderItemCommand> Items { get; set; }

        public void Validate()
        {
            AddNotifications(new Contract()
                .Requires()
                .IsGreaterThan(Customer.ToString().Length, 10, "Customer", "Cliente inválido")
                .HasLen(ZipCode.ToString(), 8, "ZipCode", "CEP inválido")
            );
        }
    }
}