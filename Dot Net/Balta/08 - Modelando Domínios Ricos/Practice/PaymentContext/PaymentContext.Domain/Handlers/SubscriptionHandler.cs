using Flunt.Notifications;
using PaymentContext.Domain.Commands;
using PaymentContext.Domain.Entities;
using PaymentContext.Domain.Enuns;
using PaymentContext.Domain.Repositories;
using PaymentContext.Domain.Services;
using PaymentContext.Domain.ValueObjects;
using PaymentContext.Shared.Commands;
using PaymentContext.Shared.Handlers;

namespace PaymentContext.Domain.Handlers
{
    public class SubscriptionHandler :
        Notifiable,
        IHandler<CreateBoletoSubscriptionCommand>,
        IHandler<CreateCreditCardSubscriptionCommand>,
        IHandler<CreatePayPalSubscriptionCommand>
    {
        private readonly IStudentRepository _repository;
        private readonly IEmailService _emailService;

        public SubscriptionHandler(IStudentRepository repository, IEmailService emailService)
        {
            _repository = repository;
            _emailService = emailService;
        }

        public ICommandResult HandleSubscription(CreateSubscriptionCommand command, Payment payment)
        {
            // Fail Fast validations
            command.Validate();
            if (command.Invalid)
            {
                AddNotifications(command);
                return new CommandResult(false, "Não foi possível realizar seu cadastro");
            }

            // Check if the document is already registered
            if (_repository.DocumentExists(command.Document))
                AddNotification("Document", "Este Documento já está em uso");

            // Check if the email is already registered
            if (_repository.EmailExists(command.Email))
                AddNotification("Email", "Este Email já está em uso");

            // Generate the VOs
            var name = new Name(command.Firstname, command.Lastname);
            var document = new Document(command.Document, EDocumentType.CPF);
            var email = new Email(command.Email);
            var address = new Address(
                command.Street,
                command.Number,
                command.City,
                command.State,
                command.Country,
                command.ZipCode
            );

            // Generate the entities
            var subscription = new Subscription(DateTime.Now.AddMonths(1));
            var student = new Student(name, document, email);

            // Create the relationships
            subscription.AddPayment(payment);
            student.AddSubscription(subscription);

            // Group the validations
            AddNotifications(name, document, email, address, payment, subscription, student);

            // Check the notifications
            if (Invalid)
                return new CommandResult(false, "Não foi possível realizar sua assinatura");

            // Save the informations
            _repository.CreateSubscription(student);

            // Send the welcome message
            _emailService.Send(
                student.Name.ToString(),
                student.Email.Address,
                "Bem Vindo",
                "Sua Assinatura foi criada"
            );

            // Return the informations
            return new CommandResult(true, "Assinatura realizada com sucesso");
        }

        public ICommandResult Handle(CreateBoletoSubscriptionCommand command)
        {
            var payment = new BoletoPayment(
                command.BarCode,
                command.BoletoNumber,
                command.PaidDate,
                command.ExpireDate,
                command.Total,
                command.TotalPaid,
                command.Payer,
                new Document(command.PayerDocument, command.PayerDocumentType),
                command.Payer,
                command.Address,
                command.EmailAddres
            );

            return HandleSubscription(command, payment);
        }

        public ICommandResult Handle(CreatePayPalSubscriptionCommand command)
        {
            var payment = new PayPalPayment(
                command.TransactionCode,
                command.PaidDate,
                command.ExpireDate,
                command.Total,
                command.TotalPaid,
                command.Payer,
                new Document(command.PayerDocument, command.PayerDocumentType),
                command.Payer,
                command.Address,
                command.EmailAddres
            );

            return HandleSubscription(command, payment);
        }

        public ICommandResult Handle(CreateCreditCardSubscriptionCommand command)
        {
            var payment = new CreditCardPayment(
                command.CardHolderNamer,
                command.CardNumber,
                command.LastTransactionNumber,
                command.PaidDate,
                command.ExpireDate,
                command.Total,
                command.TotalPaid,
                command.Payer,
                new Document(command.PayerDocument, command.PayerDocumentType),
                command.Payer,
                command.Address,
                command.EmailAddres
            );

            return HandleSubscription(command, payment);
        }

        /*public ICommandResult Handle(CreateBoletoSubscriptionCommand command)
        {
            // Fail Fast validations
            command.Validate();
            if (command.Invalid)
            {
                AddNotifications(command);
                return new CommandResult(false, "Não foi possível realizar seu cadastro");
            }

            // Check if the document is already registered
            if (_repository.DocumentExists(command.Document))
                AddNotification("Document", "Este Documento já está em uso");

            // Check if the email is already registered
            if (_repository.EmailExists(command.Email))
                AddNotification("Email", "Este Email já está em uso");

            // Generate the VOs
            var name = new Name(command.Firstname, command.Lastname);
            var document = new Document(command.Document, EDocumentType.CPF);

            // Generate the entities
            var payment = new BoletoPayment(
                command.BarCode,
                command.BoletoNumber,
                command.PaidDate,
                command.ExpireDate,
                command.Total,
                command.TotalPaid,
                command.Payer,
                new Document(command.PayerDocument, command.PayerDocumentType),
                command.Payer,
                address,
                email
            );
            var subscription = new Subscription(DateTime.Now.AddMonths(1));
            var student = new Student(name, document, command.EmailAddres);

            // Create the relationships
            subscription.AddPayment(payment);
            student.AddSubscription(subscription);

            // Group the validations
            AddNotifications(name, document, email, address, payment, subscription, student);

            // Check the notifications
            if (Invalid)
                return new CommandResult(false, "Não foi possível realizar sua assinatura");

            // Save the informations
            _repository.CreateSubscription(student);

            // Send the welcome message
            _emailService.Send(
                student.Name.ToString(),
                student.Email.Address,
                "Bem Vindo",
                "Sua Assinatura foi criada"
            );

            // Return the informations
            return new CommandResult(true, "Assinatura realizada com sucesso");
        }*/
    }
}