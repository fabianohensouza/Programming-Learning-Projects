using Flunt.Notifications;
using PaymentContext.Domain.Commands;
using PaymentContext.Shared.Commands;
using PaymentContext.Shared.Handlers;

namespace PaymentContext.Domain.Handlers
{
    public class SubscriptionHandler :
        Notifiable,
        IHandler<CreateBoletoSubscriptionCommand>
    {
        public ICommandResult Handle(CreateBoletoSubscriptionCommand command)
        {
            // Check if the document is already registered

            // Check if the email is already registered

            // Generate the VOs

            // Generate the entities

            // Apply validations

            // Save the informations

            // Send the welcome message

            // Return the informations
            return new CommandResult(true, "Assinatita realizada com sucesso");
        }
    }
}