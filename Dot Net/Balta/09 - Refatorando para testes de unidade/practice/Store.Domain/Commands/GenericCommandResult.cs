using Store.Domain.Commands.Interfaces;

namespace Store.Domain.Commands
{
    public class GenericCommandResult : ICommandResult
    {
        public GenericCommandResult(bool success, string message, object data)
        {
            Success = success;
            Messasge = message;
            Data = data;
        }                
        
        public bool Success {get; set;}
        public string Messasge { get; set; }
        public object Data { get; set; }
    }
}