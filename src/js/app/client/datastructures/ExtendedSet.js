export class ExtendedSet extends Set{
    static BUILTIN_TYPES=['undefined','symbol','string','object','number','function','boolean','bigint'];
    toString(){
        let values=Array.from(this.values());
        let isUserDefinedTypedValues=values.every(value=>{return !ExtendedSet.BUILTIN_TYPES.includes(typeof value)});
        if(isUserDefinedTypedValues){
           values=values.map(value=>{
                return typeof value.toString=="function"?value.toString():console.error("ExtendedSet toString error");
            });
        }
        return `{${values.join(',')}}`;
    }
}